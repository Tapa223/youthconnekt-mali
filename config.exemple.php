<?php
/*
 * Configuration de YouthConnekt Mali.
 *
 * 1. Copier ce fichier sous le nom config.php (dans le même dossier).
 * 2. Remplir les valeurs ci-dessous.
 * Le fichier config.php n'est jamais envoyé sur GitHub : il contient les mots de passe.
 */

return [
    // Adresse du site, sans barre finale.
    // WAMP : http://localhost/youthconnekt-mali   ·   En ligne : https://www.votre-domaine.ml
    'url_site' => 'http://localhost/youthconnekt-mali',

    // true sur ton PC (les erreurs s'affichent), false en ligne.
    'developpement' => true,

    // Base de données MySQL (WAMP : utilisateur root, mot de passe vide)
    'bdd' => [
        'hote' => 'localhost',
        'port' => 3306,
        'nom' => 'youthconnekt',
        'utilisateur' => 'root',
        'mot_de_passe' => '',
    ],

    // Envoi des e-mails (confirmation d'inscription, mot de passe oublié, formulaire de contact)
    //  - 'journal' : les e-mails ne partent pas, ils sont écrits dans stockage/emails.log
    //                et le lien s'affiche à l'écran (pratique sur WAMP) ;
    //  - 'smtp'    : envoi réel avec les informations SMTP de l'hébergeur (cPanel > Comptes e-mail) ;
    //  - 'mail'    : fonction mail() de PHP, si l'hébergeur la propose.
    'email' => [
        'mode' => 'journal',
        'expediteur' => 'noreply@votre-domaine.ml',
        'nom_expediteur' => 'YouthConnekt Mali',
        'smtp_hote' => 'mail.votre-domaine.ml',
        'smtp_port' => 465,              // 465 (SSL) ou 587 (STARTTLS)
        'smtp_securite' => 'ssl',        // 'ssl', 'tls' ou ''
        'smtp_utilisateur' => 'noreply@votre-domaine.ml',
        'smtp_mot_de_passe' => '',
        // Adresse qui reçoit une copie des messages du formulaire de contact (facultatif)
        'copie_contact' => '',
    ],
];
