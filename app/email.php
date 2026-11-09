<?php
/*
 * Envoi des e-mails : mode « journal » (fichier), « brevo » (API Brevo), « smtp » (serveur de l'hébergeur) ou « mail » (PHP).
 * Voir la rubrique 'email' de config.php.
 */

declare(strict_types=1);

/**
 * Envoie un e-mail en texte simple. Renvoie true si l'envoi a réussi.
 * $lien : lien principal du message, affiché à l'écran en mode « journal » pour tester sur WAMP.
 */
function envoyer_email(string $destinataire, string $sujet, string $texte, string $lien = '', string $repondre_a = ''): bool
{
    $mode = (string) config('email.mode', 'journal');
    $expediteur = (string) config('email.expediteur', 'noreply@localhost');
    $nom = (string) config('email.nom_expediteur', 'YouthConnekt Mali');

    if ($mode === 'journal') {
        journaliser('emails.log', "À : $destinataire\nSujet : $sujet\n\n$texte\n" . str_repeat('-', 60));
        if ($lien !== '' && en_developpement()) {
            $_SESSION['lien_email_test'] = $lien;
        }
        return true;
    }

    try {
        if ($mode === 'brevo') {
            brevo_envoyer([['email' => $destinataire]], $sujet, '', $texte, $repondre_a);
            return true;
        }
        if ($mode === 'smtp') {
            smtp_envoyer($destinataire, $sujet, $texte, $expediteur, $nom, $repondre_a);
            return true;
        }
        $entetes = [
            'From: ' . encoder_entete($nom) . ' <' . $expediteur . '>',
            'Reply-To: ' . ($repondre_a ?: $expediteur),
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ];
        $ok = mail($destinataire, encoder_entete($sujet), $texte, implode("\r\n", $entetes), '-f' . $expediteur);
        if (!$ok) {
            journaliser('erreurs.log', "E-mail non envoyé à $destinataire (fonction mail)");
        }
        return $ok;
    } catch (Throwable $e) {
        journaliser('erreurs.log', "E-mail non envoyé à $destinataire : " . $e->getMessage());
        return false;
    }
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
        throw new RuntimeException('Brevo a refusé l\'envoi (' . ($code ?: $erreur) . ') : ' . mb_substr($reponse, 0, 300));
    }
}

/** E-mail mis en page (HTML + texte) : par Brevo, SMTP ou mail() selon le mode ; écrit dans le journal en mode « journal ». */
function envoyer_email_html(string $destinataire, string $sujet, string $html, string $texte): bool
{
    $mode = (string) config('email.mode', 'journal');
    $expediteur = (string) config('email.expediteur', 'noreply@localhost');
    $nom = (string) config('email.nom_expediteur', 'YouthConnekt Mali');
    try {
        if ($mode === 'journal') {
            journaliser('emails.log', "À : $destinataire\nSujet : $sujet\n(e-mail mis en page)\n\n$texte\n" . str_repeat('-', 60));
            return true;
        }
        if ($mode === 'brevo') {
            brevo_envoyer([['email' => $destinataire]], $sujet, $html, $texte);
            return true;
        }
        if ($mode === 'smtp') {
            smtp_envoyer($destinataire, $sujet, $texte, $expediteur, $nom, '', $html);
            return true;
        }
        $limite = 'ycm' . bin2hex(random_bytes(8));
        $corps = "--$limite\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($texte))
            . "--$limite\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html)) . "--$limite--";
        $entetes = ['From: ' . encoder_entete($nom) . ' <' . $expediteur . '>', 'MIME-Version: 1.0', 'Content-Type: multipart/alternative; boundary="' . $limite . '"'];
        return mail($destinataire, encoder_entete($sujet), $corps, implode("\r\n", $entetes), '-f' . $expediteur);
    } catch (Throwable $e) {
        journaliser('erreurs.log', "E-mail non envoyé à $destinataire : " . $e->getMessage());
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
