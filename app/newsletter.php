<?php
/*
 * Newsletter : destinataires ciblés, mise en page de l'e-mail, envoi (Brevo, SMTP ou journal de test).
 * Seules les adresses abonnées (table abonnes_newsletter, actif = 1) reçoivent une newsletter :
 * le ciblage par communauté ou par type de compte réduit cette liste, il ne l'élargit jamais.
 */

declare(strict_types=1);

const CIBLES_NEWSLETTER = [
    'tous' => 'Tous les abonnés',
    'communautes' => "Les membres d'une ou plusieurs communautés",
    'individuels' => 'Les membres individuels',
    'organisations' => 'Les organisations et entreprises',
];

const STATUTS_NEWSLETTER = [
    'brouillon' => 'Brouillon',
    'envoyee' => 'Envoyée',
    'simulee' => 'Envoi de test (Brevo pas encore branché)',
];

/** Clé secrète du site (liens de désabonnement) : config.php, sinon créée une fois dans stockage/. */
function cle_site(): string
{
    $cle = (string) config('cle_secrete', '');
    if ($cle !== '') {
        return $cle;
    }
    $fichier = RACINE . '/stockage/cle-secrete.txt';
    if (!is_file($fichier)) {
        @file_put_contents($fichier, bin2hex(random_bytes(32)));
    }
    return trim((string) @file_get_contents($fichier)) ?: 'youthconnekt-mali';
}

/** Lien de désabonnement propre à une adresse (aucun compte requis pour l'utiliser). */
function lien_desabonnement(string $email): string
{
    $email = mb_strtolower($email);
    return url_absolue('/newsletter/desabonner?' . http_build_query(['e' => $email, 'j' => substr(hash_hmac('sha256', $email, cle_site()), 0, 32)]));
}

function jeton_desabonnement_valide(string $email, string $jeton): bool
{
    return hash_equals(substr(hash_hmac('sha256', mb_strtolower($email), cle_site()), 0, 32), $jeton);
}

/** Décode le ciblage et les contenus joints d'une newsletter enregistrée. */
function newsletter_preparer(array $n): array
{
    $n['communautes_ids'] = array_values(array_filter(array_map('intval', json_decode((string) ($n['communautes'] ?? ''), true) ?: [])));
    $c = json_decode((string) ($n['contenus'] ?? ''), true) ?: [];
    $n['opportunites_ids'] = array_values(array_filter(array_map('intval', $c['opportunites'] ?? [])));
    $n['actualites_ids'] = array_values(array_filter(array_map('intval', $c['actualites'] ?? [])));
    return $n;
}

/** Adresses qui recevront la newsletter, selon la cible choisie. */
function destinataires_newsletter(string $cible, array $communautes = []): array
{
    $sql = 'SELECT DISTINCT a.email FROM abonnes_newsletter a';
    $params = [];
    if ($cible === 'individuels' || $cible === 'organisations') {
        $sql .= ' JOIN utilisateurs u ON u.email = a.email AND u.actif = 1 AND u.type_compte = ?';
        $params[] = $cible === 'individuels' ? 'individuel' : 'organisation';
    } elseif ($cible === 'communautes') {
        if (!$communautes) {
            return [];
        }
        $sql .= " JOIN utilisateurs u ON u.email = a.email AND u.actif = 1
                  JOIN demandes_adhesion d ON d.utilisateur_id = u.id AND d.statut = 'acceptee' AND d.communaute_id IN (" . marqueurs($communautes) . ')';
        $params = array_merge($params, $communautes);
    }
    $sql .= ' WHERE a.actif = 1 ORDER BY a.email';
    return array_column(lignes($sql, $params), 'email');
}

/** Opportunités et actualités jointes, dans l'ordre de publication. */
function contenus_newsletter(array $n): array
{
    $opp = $n['opportunites_ids'] ? lignes('SELECT id, titre, slug, type, organisme, lieu, date_limite, resume FROM opportunites WHERE statut = \'publie\' AND id IN (' . marqueurs($n['opportunites_ids']) . ') ORDER BY date_limite IS NULL, date_limite', $n['opportunites_ids']) : [];
    $act = $n['actualites_ids'] ? lignes('SELECT id, titre, slug, chapeau, date_evenement, lieu, categorie FROM actualites WHERE statut = \'publie\' AND id IN (' . marqueurs($n['actualites_ids']) . ') ORDER BY publie_le DESC', $n['actualites_ids']) : [];
    return [$opp, $act];
}

/** Paragraphes du message : lignes vides = nouveau paragraphe, « ## » = intertitre. */
function paragraphes_newsletter(string $message): array
{
    $blocs = preg_split("/\n\s*\n/", str_replace("\r\n", "\n", trim($message))) ?: [];
    return array_values(array_filter(array_map('trim', $blocs), fn ($b) => $b !== ''));
}

/**
 * E-mail mis en page (tableaux et styles en ligne, lus par toutes les messageries).
 * $desabonnement : lien réel, ou « {{ params.desabonnement }} » pour Brevo qui le remplace pour chaque destinataire.
 */
function html_newsletter(array $n, string $desabonnement): string
{
    [$opp, $act] = contenus_newsletter($n);
    $vert = '#035E4D';
    $orange = '#F1592A';
    $encre = '#17211f';
    $texte = '#3c4a47';
    $h = fn ($t) => htmlspecialchars((string) $t, ENT_QUOTES, 'UTF-8');
    $abs = fn (string $chemin) => (str_starts_with($chemin, 'http') ? $chemin : url_absolue($chemin));

    $corps = '';
    foreach (paragraphes_newsletter((string) $n['message']) as $bloc) {
        if (str_starts_with($bloc, '## ')) {
            // Intertitre sur sa ligne ; le reste du bloc forme un paragraphe
            [$titre_bloc, $suite] = array_pad(explode("\n", $bloc, 2), 2, '');
            $corps .= '<h2 style="margin:26px 0 8px;font-size:19px;line-height:1.3;color:' . $vert . ';">' . $h(trim(substr($titre_bloc, 3))) . '</h2>';
            if (trim($suite) !== '') {
                $corps .= '<p style="margin:0 0 14px;font-size:16px;line-height:1.6;color:' . $texte . ';">' . nl2br($h(trim($suite))) . '</p>';
            }
        } else {
            $corps .= '<p style="margin:0 0 14px;font-size:16px;line-height:1.6;color:' . $texte . ';">' . nl2br($h($bloc)) . '</p>';
        }
    }
    $carte = function (string $surtitre, string $titre, string $detail, string $resume, string $lien, string $bouton) use ($h, $vert, $orange, $encre, $texte): string {
        return '<tr><td style="padding:0 0 12px;"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #e3e6e4;border-radius:12px;border-left:4px solid ' . $orange . ';"><tr><td style="padding:16px 18px;">'
            . '<p style="margin:0 0 4px;font-size:12px;font-weight:bold;letter-spacing:.06em;text-transform:uppercase;color:' . $orange . ';">' . $h($surtitre) . '</p>'
            . '<p style="margin:0 0 4px;font-size:17px;font-weight:bold;line-height:1.3;color:' . $encre . ';">' . $h($titre) . '</p>'
            . ($detail !== '' ? '<p style="margin:0 0 6px;font-size:13px;color:' . $texte . ';">' . $h($detail) . '</p>' : '')
            . ($resume !== '' ? '<p style="margin:0 0 10px;font-size:14px;line-height:1.5;color:' . $texte . ';">' . $h($resume) . '</p>' : '')
            . '<a href="' . $h($lien) . '" style="display:inline-block;padding:9px 16px;border-radius:999px;background:' . $vert . ';color:#ffffff;font-size:14px;font-weight:bold;text-decoration:none;">' . $h($bouton) . '</a>'
            . '</td></tr></table></td></tr>';
    };
    $section = function (string $titre, string $cartes) use ($vert): string {
        return '<h2 style="margin:28px 0 12px;font-size:19px;color:' . $vert . ';">' . $titre . '</h2><table role="presentation" width="100%" cellspacing="0" cellpadding="0">' . $cartes . '</table>';
    };
    if ($opp) {
        $cartes = '';
        foreach ($opp as $o) {
            $detail = implode(' · ', array_filter([$o['organisme'] ?? '', $o['lieu'] ?? '', $o['date_limite'] ? 'Date limite : ' . date_longue($o['date_limite']) : '']));
            $cartes .= $carte(types_opportunite()[$o['type']]['nom'] ?? 'Opportunité', $o['titre'], $detail, couper((string) ($o['resume'] ?? ''), 160), url_absolue('/opportunites/' . $o['slug']), "Voir l'opportunité");
        }
        $corps .= $section('Opportunités', $cartes);
    }
    if ($act) {
        $cartes = '';
        foreach ($act as $a) {
            $detail = implode(' · ', array_filter([$a['date_evenement'] ? date_longue($a['date_evenement']) : '', $a['lieu'] ?? '']));
            $cartes .= $carte($a['categorie'] === 'evenement' ? 'Événement' : 'Actualité', $a['titre'], $detail, couper((string) ($a['chapeau'] ?? ''), 160), url_absolue('/actualites/' . $a['slug']), 'Lire la suite');
        }
        $corps .= $section('Actualités et événements', $cartes);
    }
    $image = !empty($n['image']) ? '<tr><td><img src="' . $h($abs(media($n['image']))) . '" alt="" width="600" style="display:block;width:100%;max-width:600px;height:auto;border:0;"></td></tr>' : '';

    return '<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . $h($n['objet']) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#f3f5f4;font-family:Arial,Helvetica,sans-serif;">'
        . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f5f4;"><tr><td align="center" style="padding:24px 12px;">'
        . '<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;">'
        . '<tr><td style="padding:20px 28px;border-bottom:4px solid ' . $orange . ';"><img src="' . $h($abs(asset('img/logo-youthconnekt-mali.png'))) . '" alt="YouthConnekt Mali" height="44" style="display:block;height:44px;width:auto;border:0;"></td></tr>'
        . $image
        . '<tr><td style="padding:28px 28px 8px;">'
        . '<h1 style="margin:0 0 16px;font-size:25px;line-height:1.25;color:' . $vert . ';">' . $h($n['titre'] ?: $n['objet']) . '</h1>'
        . $corps
        . '</td></tr>'
        . '<tr><td style="padding:22px 28px;background:' . $vert . ';color:#ffffff;font-size:13px;line-height:1.6;">'
        . '<p style="margin:0 0 6px;font-weight:bold;">YouthConnekt Mali</p>'
        . '<p style="margin:0 0 10px;color:#d7e6e2;">Chapitre national de YouthConnekt Africa, avec le ministère de la Jeunesse et des Sports et le PNUD.</p>'
        . '<p style="margin:0;color:#d7e6e2;">Vous recevez cet e-mail car vous êtes abonné à la newsletter. <a href="' . $desabonnement . '" style="color:#ffffff;font-weight:bold;">Se désabonner</a></p>'
        . '</td></tr></table></td></tr></table></body></html>';
}

/** Version texte (messageries qui n'affichent pas le HTML). */
function texte_newsletter(array $n, string $desabonnement): string
{
    [$opp, $act] = contenus_newsletter($n);
    $t = ($n['titre'] ?: $n['objet']) . "\n\n";
    foreach (paragraphes_newsletter((string) $n['message']) as $bloc) {
        if (str_starts_with($bloc, '## ')) {
            [$titre_bloc, $suite] = array_pad(explode("\n", $bloc, 2), 2, '');
            $bloc = mb_strtoupper(trim(substr($titre_bloc, 3))) . ($suite !== '' ? "\n" . $suite : '');
        }
        $t .= $bloc . "\n\n";
    }
    foreach ($opp as $o) {
        $t .= '- ' . $o['titre'] . ($o['date_limite'] ? ' (date limite : ' . date_longue($o['date_limite']) . ')' : '') . "\n  " . url_absolue('/opportunites/' . $o['slug']) . "\n";
    }
    foreach ($act as $a) {
        $t .= '- ' . $a['titre'] . "\n  " . url_absolue('/actualites/' . $a['slug']) . "\n";
    }
    return $t . "\n--\nYouthConnekt Mali. Se désabonner : $desabonnement\n";
}

/**
 * Envoie la newsletter aux adresses données. Renvoie [nombre envoyé, mode utilisé].
 * Brevo : par lots de 500, lien de désabonnement propre à chacun ; SMTP / mail : une adresse à la fois ;
 * journal (WAMP, Brevo pas encore branché) : rien ne part, l'envoi est écrit dans stockage/emails.log.
 */
function envoyer_newsletter(array $n, array $adresses): array
{
    $mode = (string) config('email.mode', 'journal');
    if ($mode === 'journal') {
        journaliser('emails.log', 'Newsletter « ' . $n['objet'] . ' » (simulation) à ' . count($adresses) . " adresse(s) :\n" . implode(', ', array_slice($adresses, 0, 50)) . (count($adresses) > 50 ? '…' : '') . "\n" . str_repeat('-', 60));
        return [count($adresses), 'journal'];
    }
    $envoyes = 0;
    if ($mode === 'brevo') {
        $html = html_newsletter($n, '{{ params.desabonnement }}');
        $texte = texte_newsletter($n, '{{ params.desabonnement }}');
        foreach (array_chunk($adresses, 500) as $lot) {
            brevo_envoyer(array_map(fn ($e) => ['email' => $e, 'params' => ['desabonnement' => lien_desabonnement($e)]], $lot), $n['objet'], $html, $texte);
            $envoyes += count($lot);
        }
        return [$envoyes, 'brevo'];
    }
    foreach ($adresses as $e) {
        $lien = lien_desabonnement($e);
        if (envoyer_email_html($e, $n['objet'], html_newsletter($n, htmlspecialchars($lien, ENT_QUOTES, 'UTF-8')), texte_newsletter($n, $lien))) {
            $envoyes++;
        }
    }
    return [$envoyes, $mode];
}
