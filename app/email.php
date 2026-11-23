<?php
/*
 * Envoi des e-mails : mode « journal » (fichier), « brevo » (API Brevo), « smtp » (serveur de l'hébergeur) ou « mail » (PHP).
 * Voir la rubrique 'email' de config.php.
 */

declare(strict_types=1);

/**
 * Envoie un e-mail écrit en texte simple : il part mis en page (logo en haut, coordonnées du chapitre en bas),
 * avec sa version texte pour les messageries qui n'affichent pas le HTML. Renvoie true si l'envoi a réussi.
 * $lien : lien principal du message (bouton dans l'e-mail ; affiché à l'écran en mode « journal » sur WAMP).
 * $categorie : nature de l'e-mail pour le journal des envois (« Réponse à un message », « Mot de passe »…).
 */
function envoyer_email(string $destinataire, string $sujet, string $texte, string $lien = '', string $repondre_a = '', string $categorie = ''): bool
{
    if ($lien !== '' && en_developpement() && (string) config('email.mode', 'journal') === 'journal') {
        $_SESSION['lien_email_test'] = $lien;
    }
    $texte_brut = $lien !== '' && !str_contains($texte, $lien) ? $texte . "\n\n" . $lien : $texte;
    return envoyer_email_html($destinataire, $sujet, gabarit_email($sujet, texte_vers_html($texte, $lien)), $texte_brut . pied_email_texte(), $repondre_a, $categorie);
}

/** Coordonnées du chapitre affichées au bas des e-mails (prises dans Paramètres). */
function coordonnees_email(): array
{
    $p = parametres();
    $reseaux = array_filter(['Facebook' => $p['lien_facebook'] ?? '', 'Instagram' => $p['lien_instagram'] ?? '', 'LinkedIn' => $p['lien_linkedin'] ?? '',
        'X' => $p['lien_x'] ?? '', 'YouTube' => $p['lien_youtube'] ?? '', 'TikTok' => $p['lien_tiktok'] ?? '']);
    return [
        'email' => (string) (($p['email_contact'] ?? '') ?: config('email.copie_contact', '')),
        'site' => rtrim(url_absolue('/'), '/'),
        'reseaux' => $reseaux,
        'logo' => url_absolue(asset('img/logo-youthconnekt-mali.png')),
    ];
}

/** Texte d'un e-mail simple remis en HTML : paragraphes, liens cliquables, et le lien principal en bouton. */
function texte_vers_html(string $texte, string $lien = ''): string
{
    $html = '';
    foreach (preg_split("/\n\s*\n/", trim($texte)) as $bloc) {
        $bloc = htmlspecialchars($bloc, ENT_QUOTES, 'UTF-8');
        $bloc = preg_replace('#https?://[^\s<]+#', '<a href="$0" style="color:#035E4D;font-weight:bold;word-break:break-all;">$0</a>', $bloc);
        $html .= '<p style="margin:0 0 14px;">' . nl2br($bloc) . '</p>';
    }
    if ($lien !== '') {
        $html .= '<p style="margin:22px 0 8px;"><a href="' . htmlspecialchars($lien, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background:#F1592A;color:#ffffff;font-weight:bold;text-decoration:none;padding:12px 22px;border-radius:999px;">Ouvrir le lien</a></p>';
    }
    return $html;
}

/** Bas commun à tous les e-mails : logo, nom du chapitre, adresse de contact, site et réseaux. */
function pied_email_html(string $mention = '', bool $avec_logo = true): string
{
    $c = coordonnees_email();
    $h = fn ($t) => htmlspecialchars((string) $t, ENT_QUOTES, 'UTF-8');
    $site_affiche = preg_replace('#^https?://#', '', $c['site']);
    $reseaux = implode(' &nbsp;·&nbsp; ', array_map(fn ($nom, $url) => '<a href="' . $h($url) . '" style="color:#035E4D;text-decoration:none;font-weight:bold;">' . $h($nom) . '</a>', array_keys($c['reseaux']), $c['reseaux']));
    return '<tr><td style="padding:22px 28px 24px;border-top:1px solid #e3e9e7;background:#f7f9f8;font-size:13px;line-height:1.6;color:#3c4a47;">'
        . ($avec_logo ? '<img src="' . $h($c['logo']) . '" alt="YouthConnekt Mali" height="40" style="display:block;height:40px;width:auto;border:0;margin:0 0 10px;">' : '')
        . '<p style="margin:0;font-weight:bold;color:#17211f;">YouthConnekt Mali</p>'
        . '<p style="margin:2px 0 0;">Chapitre national de YouthConnekt Africa</p>'
        . '<p style="margin:8px 0 0;">'
        . ($c['email'] !== '' ? '<a href="mailto:' . $h($c['email']) . '" style="color:#035E4D;font-weight:bold;text-decoration:none;">' . $h($c['email']) . '</a> &nbsp;·&nbsp; ' : '')
        . '<a href="' . $h($c['site']) . '" style="color:#035E4D;font-weight:bold;text-decoration:none;">' . $h($site_affiche) . '</a></p>'
        . ($reseaux !== '' ? '<p style="margin:6px 0 0;">' . $reseaux . '</p>' : '')
        . ($mention !== '' ? '<p style="margin:12px 0 0;font-size:12px;color:#5b6a67;">' . $mention . '</p>' : '')
        . '</td></tr>';
}

/** Même pied, pour la version texte des e-mails. */
function pied_email_texte(): string
{
    $c = coordonnees_email();
    return "\n\n--\nYouthConnekt Mali\n" . ($c['email'] !== '' ? $c['email'] . "\n" : '') . $c['site'] . "\n";
}

/** Gabarit HTML commun : trait orange, contenu, puis le pied avec le logo et les coordonnées du chapitre. */
function gabarit_email(string $titre, string $contenu_html, string $mention_pied = ''): string
{
    $h = fn ($t) => htmlspecialchars((string) $t, ENT_QUOTES, 'UTF-8');
    return '<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . $h($titre) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#f3f5f4;font-family:Arial,Helvetica,sans-serif;">'
        . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f5f4;"><tr><td align="center" style="padding:24px 12px;">'
        . '<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;">'
        . '<tr><td style="height:5px;line-height:5px;font-size:0;background:#F1592A;">&nbsp;</td></tr>'
        . '<tr><td style="padding:28px 28px 12px;font-size:15px;line-height:1.65;color:#17211f;">' . $contenu_html . '</td></tr>'
        . pied_email_html($mention_pied)
        . '</table></td></tr></table></body></html>';
}

/** Note chaque e-mail parti (ou non) dans le journal des envois de l'administration. Jamais le contenu. */
function noter_envoi(string $destinataire, string $sujet, string $categorie, bool $ok, string $erreur = ''): void
{
    try {
        inserer('emails_envoyes', ['destinataire' => mb_substr($destinataire, 0, 190), 'objet' => mb_substr($sujet, 0, 255),
            'categorie' => mb_substr($categorie !== '' ? $categorie : 'Autre', 0, 60), 'ok' => $ok ? 1 : 0,
            'erreur' => mb_substr($erreur, 0, 300), 'mode' => (string) config('email.mode', 'journal'), 'envoye_le' => date('Y-m-d H:i:s')]);
    } catch (Throwable $e) {
        // table pas encore créée (mise à jour 34) : l'envoi n'en dépend pas
    }
}

/** Raison du dernier échec d'envoi (réponse de Brevo, du serveur SMTP…), pour l'afficher à l'administration. */
function derniere_erreur_email(): string
{
    return (string) ($GLOBALS['derniere_erreur_email'] ?? '');
}

function encoder_entete(string $texte): string
{
    return preg_match('/[^\x20-\x7E]/', $texte) ? '=?UTF-8?B?' . base64_encode($texte) . '?=' : $texte;
}

/** Petit client SMTP (SSL ou STARTTLS, authentification LOGIN). */
/**
 * Envoi par l'API de Brevo (https://api.brevo.com/v3/smtp/email).
 * $versions : [['email' => …, 'params' => [...]], …] ; le HTML peut contenir {{ params.nom }} (lien de désabonnement…).
 * Jusqu'à 1 000 destinataires par appel, chacun reçoit son propre exemplaire.
 */
function brevo_envoyer(array $versions, string $sujet, string $html, string $texte = '', string $repondre_a = ''): void
{
    $cle = (string) config('email.brevo_cle', '');
    if ($cle === '') {
        throw new RuntimeException("Clé API Brevo absente : renseignez 'brevo_cle' dans config.php.");
    }
    $corps = [
        'sender' => ['name' => (string) config('email.nom_expediteur', 'YouthConnekt Mali'), 'email' => (string) config('email.expediteur')],
        'subject' => $sujet,
    ];
    if ($html !== '') {
        $corps['htmlContent'] = $html;
    }
    if ($texte !== '') {
        $corps['textContent'] = $texte;
    }
    if ($repondre_a !== '') {
        $corps['replyTo'] = ['email' => $repondre_a];
    }
    if (count($versions) === 1 && empty($versions[0]['params'])) {
        $corps['to'] = [['email' => $versions[0]['email']]];
    } else {
        $corps['messageVersions'] = array_map(fn ($v) => ['to' => [['email' => $v['email']]]] + (!empty($v['params']) ? ['params' => $v['params']] : []), $versions);
    }
    $json = json_encode($corps, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $entetes = ['api-key: ' . $cle, 'Content-Type: application/json', 'Accept: application/json'];
    if (function_exists('curl_init')) {
        $c = curl_init('https://api.brevo.com/v3/smtp/email');
        curl_setopt_array($c, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $json, CURLOPT_HTTPHEADER => $entetes, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30]);
        $reponse = (string) curl_exec($c);
        $code = (int) curl_getinfo($c, CURLINFO_HTTP_CODE);
        $erreur = curl_error($c);
        curl_close($c);
    } else {
        $reponse = (string) @file_get_contents('https://api.brevo.com/v3/smtp/email', false, stream_context_create(['http' => [
            'method' => 'POST', 'header' => implode("\r\n", $entetes), 'content' => $json, 'timeout' => 30, 'ignore_errors' => true]]));
        $code = (int) (preg_match('#HTTP/\S+ (\d{3})#', $http_response_header[0] ?? '', $m) ? $m[1] : 0);
        $erreur = '';
    }
    if ($code < 200 || $code >= 300) {
        $piste = match (true) {
            $code === 0 => " Le serveur n'arrive pas à joindre Brevo.",
            (bool) preg_match('/IP/i', $reponse) => ' Autorisez l\'adresse IP du serveur dans Brevo : Paramètres > Sécurité > IP autorisées.',
            $code === 401 => ' La clé API est incorrecte : vérifiez brevo_cle dans config.php.',
            (bool) preg_match('/sender/i', $reponse) => ' L\'expéditeur n\'est pas validé dans Brevo (Expéditeurs, domaines et IP dédiées).',
            $code === 402 || (bool) preg_match('/credit/i', $reponse) => ' Le quota d\'envoi Brevo est épuisé pour aujourd\'hui.',
            default => '',
        };
        throw new RuntimeException('Brevo a refusé l\'envoi (' . ($code ?: $erreur) . ') : ' . mb_substr($reponse, 0, 300) . $piste);
    }
}

/** E-mail mis en page (HTML + texte) : par Brevo, SMTP ou mail() selon le mode ; écrit dans le journal en mode « journal ». */
function envoyer_email_html(string $destinataire, string $sujet, string $html, string $texte, string $repondre_a = '', string $categorie = ''): bool
{
    $mode = (string) config('email.mode', 'journal');
    $expediteur = (string) config('email.expediteur', 'noreply@localhost');
    $nom = (string) config('email.nom_expediteur', 'YouthConnekt Mali');
    try {
        if ($mode === 'journal') {
            journaliser('emails.log', "À : $destinataire\nSujet : $sujet\n(e-mail mis en page)\n\n$texte\n" . str_repeat('-', 60));
        } elseif ($mode === 'brevo') {
            brevo_envoyer([['email' => $destinataire]], $sujet, $html, $texte, $repondre_a);
        } elseif ($mode === 'smtp') {
            smtp_envoyer($destinataire, $sujet, $texte, $expediteur, $nom, $repondre_a, $html);
        } else {
            $limite = 'ycm' . bin2hex(random_bytes(8));
            $corps = "--$limite\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($texte))
                . "--$limite\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html)) . "--$limite--";
            $entetes = ['From: ' . encoder_entete($nom) . ' <' . $expediteur . '>', 'Reply-To: ' . ($repondre_a ?: $expediteur), 'MIME-Version: 1.0', 'Content-Type: multipart/alternative; boundary="' . $limite . '"'];
            if (!mail($destinataire, encoder_entete($sujet), $corps, implode("\r\n", $entetes), '-f' . $expediteur)) {
                throw new RuntimeException('La fonction mail() du serveur a refusé l\'envoi.');
            }
        }
        noter_envoi($destinataire, $sujet, $categorie, true);
        return true;
    } catch (Throwable $e) {
        $GLOBALS['derniere_erreur_email'] = $e->getMessage();
        journaliser('erreurs.log', "E-mail non envoyé à $destinataire : " . $e->getMessage());
        noter_envoi($destinataire, $sujet, $categorie, false, $e->getMessage());
        return false;
    }
}

function smtp_envoyer(string $a, string $sujet, string $texte, string $de, string $nom, string $repondre_a = '', string $html = ''): void
{
    $hote = (string) config('email.smtp_hote');
    $port = (int) config('email.smtp_port', 465);
    $securite = (string) config('email.smtp_securite', 'ssl');
    $adresse = ($securite === 'ssl' ? 'ssl://' : 'tcp://') . $hote . ':' . $port;
    $contexte = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true]]);
    $flux = @stream_socket_client($adresse, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $contexte);
    if (!$flux) {
        throw new RuntimeException("Connexion SMTP impossible ($errstr)");
    }
    stream_set_timeout($flux, 15);

    $lire = function () use ($flux): string {
        $reponse = '';
        while (($ligne = fgets($flux, 515)) !== false) {
            $reponse .= $ligne;
            if (isset($ligne[3]) && $ligne[3] === ' ') {
                break;
            }
        }
        return $reponse;
    };
    $commande = function (string $cmd, array $attendus) use ($flux, $lire): string {
        if ($cmd !== '') {
            fwrite($flux, $cmd . "\r\n");
        }
        $r = $lire();
        if (!in_array((int) substr($r, 0, 3), $attendus, true)) {
            throw new RuntimeException('Réponse SMTP inattendue : ' . trim($r));
        }
        return $r;
    };

    $nom_machine = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $commande('', [220]);
    $commande('EHLO ' . $nom_machine, [250]);
    if ($securite === 'tls') {
        $commande('STARTTLS', [220]);
        if (!stream_socket_enable_crypto($flux, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('Chiffrement TLS impossible');
        }
        $commande('EHLO ' . $nom_machine, [250]);
    }
    $utilisateur = (string) config('email.smtp_utilisateur', '');
    if ($utilisateur !== '') {
        $commande('AUTH LOGIN', [334]);
        $commande(base64_encode($utilisateur), [334]);
        $commande(base64_encode((string) config('email.smtp_mot_de_passe', '')), [235]);
    }
    $commande('MAIL FROM:<' . $de . '>', [250]);
    $commande('RCPT TO:<' . $a . '>', [250, 251]);
    $commande('DATA', [354]);

    $entetes = [
        'Date: ' . date('r'),
        'From: ' . encoder_entete($nom) . ' <' . $de . '>',
        'To: <' . $a . '>',
        'Reply-To: ' . ($repondre_a ?: $de),
        'Subject: ' . encoder_entete($sujet),
        'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . (explode('@', $de)[1] ?? 'localhost') . '>',
        'MIME-Version: 1.0',
    ];
    $texte_crlf = str_replace("\n", "\r\n", str_replace("\r\n", "\n", $texte));
    if ($html !== '') {
        // Texte et HTML : le logiciel de messagerie affiche la version qu'il sait lire
        $limite = 'ycm' . bin2hex(random_bytes(8));
        $entetes[] = 'Content-Type: multipart/alternative; boundary="' . $limite . '"';
        $corps = "--$limite\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($texte_crlf))
            . "--$limite\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html)) . "--$limite--";
    } else {
        $entetes[] = 'Content-Type: text/plain; charset=UTF-8';
        $entetes[] = 'Content-Transfer-Encoding: base64';
        $corps = chunk_split(base64_encode($texte_crlf));
    }
    fwrite($flux, implode("\r\n", $entetes) . "\r\n\r\n" . $corps . "\r\n.\r\n");
    $commande('', [250]);
    $commande('QUIT', [221]);
    fclose($flux);
}
