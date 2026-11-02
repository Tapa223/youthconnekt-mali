<?php
/*
 * Envoi des e-mails : mode « journal » (fichier), « smtp » (serveur de l'hébergeur) ou « mail » (PHP).
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
function smtp_envoyer(string $a, string $sujet, string $texte, string $de, string $nom, string $repondre_a = ''): void
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
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: base64',
    ];
    $corps = chunk_split(base64_encode(str_replace("\n", "\r\n", str_replace("\r\n", "\n", $texte))));
    fwrite($flux, implode("\r\n", $entetes) . "\r\n\r\n" . $corps . "\r\n.\r\n");
    $commande('', [250]);
    $commande('QUIT', [221]);
    fclose($flux);
}
