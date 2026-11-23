#!/usr/local/bin/php -q
<?php
/*
 * Réception des e-mails écrits directement à l'adresse de contact (contact@youthconnekt.ml).
 *
 * Le serveur de messagerie confie chaque e-mail reçu à ce script (cPanel > Redirecteurs >
 * « Diriger vers un programme »). Le script le range dans « Messages de contact » de l'administration,
 * avec la mention « Reçu par e-mail ». L'e-mail reste aussi dans la boîte contact (webmail).
 *
 * Écartés : spams signalés par le serveur, réponses automatiques, retours d'erreur, listes de diffusion,
 * et les copies que le site s'envoie lui-même (le message est déjà dans l'administration).
 *
 * Ce script ne s'exécute qu'en ligne de commande, jamais depuis un navigateur (le dossier app/ est fermé au web).
 * Il ne doit rien afficher : toute sortie ferait renvoyer l'e-mail en erreur à l'expéditeur.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

ob_start();
try {
    require __DIR__ . '/noyau.php';
    require __DIR__ . '/email.php';
    $brut = stream_get_contents(STDIN, 15 * 1024 * 1024);
    if (is_string($brut) && trim($brut) !== '') {
        recevoir_email($brut);
    }
} catch (Throwable $e) {
    @file_put_contents(dirname(__DIR__) . '/stockage/erreurs.log', '[' . date('Y-m-d H:i:s') . '] Réception d\'un e-mail : ' . $e->getMessage() . "\n", FILE_APPEND);
}
ob_end_clean();
exit(0);

/** Range un e-mail brut dans les messages de contact (ou l'écarte). */
function recevoir_email(string $brut): void
{
    [$entetes, $corps] = email_separer($brut);
    $h = email_entetes($entetes);

    // Écartés : spams, réponses automatiques, retours d'erreur, listes de diffusion
    if (preg_match('/^yes/i', $h['x-spam-flag'] ?? '') || preg_match('/^yes/i', $h['x-spam-status'] ?? '')) return;
    if (isset($h['auto-submitted']) && !preg_match('/^no/i', $h['auto-submitted'])) return;
    if (preg_match('/^(bulk|list|junk|auto_reply)/i', $h['precedence'] ?? '') || isset($h['list-id']) || isset($h['x-autoreply']) || isset($h['x-autorespond'])) return;

    [$nom, $adresse] = email_adresse(email_entete_decoder($h['reply-to'] ?? '') ?: email_entete_decoder($h['from'] ?? ''));
    if ($adresse === '' || preg_match('/^(mailer-daemon|postmaster|no-?reply)@/i', $adresse)) return;
    // Les copies que le site s'envoie (formulaire de contact, propositions) sont déjà dans l'administration
    if (mb_strtolower($adresse) === mb_strtolower((string) config('email.expediteur', ''))) return;
    [, $de] = email_adresse(email_entete_decoder($h['from'] ?? ''));
    if (mb_strtolower($de) === mb_strtolower((string) config('email.expediteur', '')) || preg_match('/^no-?reply@/i', $de)) return;

    $id_email = mb_substr(trim($h['message-id'] ?? ''), 0, 255);
    if ($id_email !== '' && valeur('SELECT COUNT(*) FROM messages_contact WHERE message_id_email = ?', [$id_email])) return;

    $pieces = 0;
    $texte = email_texte($h, $corps, $pieces);
    $texte = trim(preg_replace("/\n{3,}/", "\n\n", str_replace("\r", '', $texte)));
    if ($pieces > 0) {
        $texte .= "\n\n[" . $pieces . ' pièce' . ($pieces > 1 ? 's jointes' : ' jointe') . ' : à ouvrir dans la boîte contact (webmail)]';
    }
    $objet = trim(email_entete_decoder($h['subject'] ?? ''));

    inserer('messages_contact', [
        'nom' => mb_substr($nom !== '' ? $nom : strstr($adresse, '@', true), 0, 120),
        'profil' => 'email',
        'email' => mb_substr($adresse, 0, 190),
        'objet' => mb_substr($objet !== '' ? $objet : '(sans objet)', 0, 200),
        'message' => mb_substr($texte !== '' ? $texte : '(message vide)', 0, 20000),
        'recu_le' => date('Y-m-d H:i:s'),
        'traite' => 0,
        'source' => 'email',
        'message_id_email' => $id_email,
    ]);
}

/** En-têtes et corps d'un e-mail (ou d'une partie). */
function email_separer(string $brut): array
{
    $brut = str_replace("\r\n", "\n", $brut);
    $pos = strpos($brut, "\n\n");
    return $pos === false ? [$brut, ''] : [substr($brut, 0, $pos), substr($brut, $pos + 2)];
}

/** En-têtes dépliés, noms en minuscules (premier exemplaire de chaque en-tête). */
function email_entetes(string $bloc): array
{
    $bloc = preg_replace("/\n[ \t]+/", ' ', $bloc);
    $h = [];
    foreach (explode("\n", $bloc) as $ligne) {
        if (preg_match('/^([A-Za-z0-9-]+):\s*(.*)$/', $ligne, $m)) {
            $h[strtolower($m[1])] ??= trim($m[2]);
        }
    }
    return $h;
}

/** En-tête encodé (=?UTF-8?B?…?=, =?ISO-8859-1?Q?…?=) en texte UTF-8. */
function email_entete_decoder(string $valeur): string
{
    if ($valeur === '') return '';
    if (!str_contains($valeur, '=?')) {
        return mb_check_encoding($valeur, 'UTF-8') ? $valeur : mb_convert_encoding($valeur, 'UTF-8', 'ISO-8859-1');
    }
    $decode = function_exists('iconv_mime_decode') ? @iconv_mime_decode($valeur, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') : false;
    if ($decode === false) $decode = mb_decode_mimeheader($valeur);
    return mb_check_encoding($decode, 'UTF-8') ? $decode : mb_convert_encoding($decode, 'UTF-8', 'ISO-8859-1');
}

/** « Awa Diarra <awa@exemple.ml> » → ['Awa Diarra', 'awa@exemple.ml'] */
function email_adresse(string $valeur): array
{
    if (preg_match('/^\s*"?([^"<]*?)"?\s*<([^>]+)>/', $valeur, $m)) {
        $adresse = trim($m[2]);
        $nom = trim($m[1]);
    } else {
        $adresse = trim($valeur);
        $nom = '';
    }
    $adresse = mb_strtolower($adresse);
    return [$nom, filter_var($adresse, FILTER_VALIDATE_EMAIL) ? $adresse : ''];
}

/** Paramètre d'un en-tête : charset, boundary, name… */
function email_parametre(string $entete, string $nom): string
{
    return preg_match('/(?:^|;)\s*' . preg_quote($nom, '/') . '\*?="?([^";]+)"?/i', $entete, $m) ? trim($m[1]) : '';
}

/** Texte lisible d'un e-mail : la partie texte de préférence, sinon le HTML nettoyé. Compte les pièces jointes. */
function email_texte(array $h, string $corps, int &$pieces): string
{
    $type = strtolower($h['content-type'] ?? 'text/plain');
    $disposition = strtolower($h['content-disposition'] ?? '');

    if (str_starts_with($type, 'multipart/')) {
        $limite = email_parametre($h['content-type'], 'boundary');
        if ($limite === '') return '';
        $morceaux = preg_split('/^--' . preg_quote($limite, '/') . '(?:--)?\s*$/m', $corps);
        $textes = [];
        foreach (array_slice($morceaux, 1) as $morceau) {
            if (trim($morceau) === '') continue;
            [$e, $c] = email_separer(ltrim($morceau, "\n"));
            $hp = email_entetes($e);
            $tp = strtolower($hp['content-type'] ?? 'text/plain');
            $textes[] = ['type' => $tp, 'texte' => email_texte($hp, $c, $pieces)];
        }
        if (str_starts_with($type, 'multipart/alternative')) {
            foreach ($textes as $t) {
                if (str_starts_with($t['type'], 'text/plain') && trim($t['texte']) !== '') return $t['texte'];
            }
        }
        return implode("\n\n", array_filter(array_column($textes, 'texte'), fn ($t) => trim($t) !== ''));
    }

    // Pièce jointe : comptée, pas affichée
    if (str_starts_with($disposition, 'attachment') || (!str_starts_with($type, 'text/') && !str_starts_with($type, 'message/'))
        || (email_parametre($h['content-type'] ?? '', 'name') !== '' && !str_starts_with($disposition, 'inline'))) {
        $pieces++;
        return '';
    }

    $encodage = strtolower(trim($h['content-transfer-encoding'] ?? ''));
    if ($encodage === 'base64') {
        $corps = (string) base64_decode(preg_replace('/\s+/', '', $corps));
    } elseif ($encodage === 'quoted-printable') {
        $corps = quoted_printable_decode($corps);
    }
    $charset = email_parametre($h['content-type'] ?? '', 'charset') ?: 'UTF-8';
    if (strtoupper($charset) !== 'UTF-8' || !mb_check_encoding($corps, 'UTF-8')) {
        $converti = @mb_convert_encoding($corps, 'UTF-8', strtoupper($charset) === 'UTF-8' ? 'ISO-8859-1' : $charset);
        $corps = is_string($converti) ? $converti : mb_convert_encoding($corps, 'UTF-8', 'ISO-8859-1');
    }
    if (str_starts_with($type, 'text/html')) {
        $corps = preg_replace('#<(style|script|head)[^>]*>.*?</\1>#is', '', $corps);
        $corps = preg_replace('#<br\s*/?>#i', "\n", $corps);
        $corps = preg_replace('#</(p|div|tr|li|h[1-6]|blockquote)>#i', "\n\n", $corps);
        $corps = html_entity_decode(strip_tags($corps), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $corps = preg_replace("/[ \t]+/", ' ', $corps);
        $corps = preg_replace("/\n[ \t]+/", "\n", $corps);
    }
    return $corps;
}
