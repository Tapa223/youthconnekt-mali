<?php
/*
 * Pages institutionnelles : accueil, À propos, contact, pages légales, newsletter.
 */

declare(strict_types=1);

function accueil(): void
{
    // Initiatives : celles mises en avant, complétées par les plus récentes (3 au total)
    $initiatives = initiatives_publiees('ORDER BY i.mis_en_avant DESC, i.publie_le DESC LIMIT 3');

    $opportunites = opportunites_ouvertes("AND mis_en_avant = 1 ORDER BY date_limite IS NULL, date_limite, publie_le DESC LIMIT 3");
    if (!$opportunites) {
        $opportunites = opportunites_ouvertes('ORDER BY date_limite IS NULL, date_limite, publie_le DESC LIMIT 3');
    }

    // L'actualité à la une : celle mise en avant, sinon la plus récente
    $actualites = actualites_publiees('ORDER BY mis_en_avant DESC, publie_le DESC LIMIT 4');

    $partenaires = lignes('SELECT * FROM partenaires WHERE actif = 1 ORDER BY ordre, nom');

    afficher(vue('pages/accueil', [
        'titre' => 'YouthConnekt Mali | La jeunesse malienne se connecte ici',
        'p' => parametres(),
        'chiffres' => lignes('SELECT * FROM chiffres_cles WHERE publie = 1 ORDER BY ordre, id LIMIT 3'),
        'axes' => array_slice(axes(), 0, 3),
        'initiatives' => $initiatives,
        'mot' => mot_ministre(),
        'communautes' => communautes_actives(6),
        'nb_communautes' => (int) valeur('SELECT COUNT(*) FROM communautes WHERE active = 1'),
        'opportunites' => $opportunites,
        'actualite_une' => $actualites[0] ?? null,
        'actualites' => array_slice($actualites, 1, 3),
        'partenaires' => $partenaires,
        // Tant qu'aucun logo n'est saisi dans l'administration, seuls les partenaires connus sont affichés
        'partenaires_maquette' => ['PNUD', 'Ministère de la Jeunesse et des Sports'],
        'temoignages' => lignes('SELECT * FROM temoignages WHERE publie = 1 ORDER BY ordre, id DESC LIMIT 3'),
    ]));
}

function a_propos(): void
{
    $mot = mot_ministre();
    afficher(vue('pages/a_propos', [
        'titre' => 'À propos | YouthConnekt Mali',
        'description' => "YouthConnekt Mali, chapitre national de YouthConnekt Africa : mission, valeurs, axes d'action et parcours.",
        'obstacles' => [
            ["L'information est dispersée", 'Formations, bourses et appels à candidatures existent, mais les jeunes ne savent pas toujours où les trouver.'],
            ['Les réseaux sont difficiles d\'accès', 'Rencontrer un mentor, un partenaire ou un décideur reste rare pour beaucoup de jeunes.'],
            ['Les talents restent invisibles', 'De nombreux projets portés par des jeunes avancent sans que personne ne les connaisse.'],
        ],
        'publics' => [
            ['Pour les jeunes', 'Trouver une formation, une bourse ou un stage, rencontrer des mentors, faire connaître son projet et prendre part aux décisions qui les concernent.'],
            ['Pour les organisations', 'Toucher des jeunes motivés partout dans le pays, partager leurs opportunités et monter des initiatives avec le chapitre.'],
            ['Avec les institutions', 'Faire remonter la voix des jeunes, contribuer aux politiques jeunesse et relier les programmes publics aux jeunes qui en ont besoin.'],
        ],
        'approche' => [
            ['Repérer', 'Nous recherchons formations, bourses, stages et appels utiles aux jeunes.'],
            ['Partager', 'Nous les relayons sur la plateforme, par e-mail et dans les communautés.'],
            ['Accompagner', 'Ateliers, mentorat et événements pour aider chacun à saisir sa chance.'],
            ['Mettre en lumière', 'Nous faisons connaître les réussites et les projets des jeunes.'],
        ],
        'parcours' => [
            ['2024', 'Lancement du chapitre', 'Naissance de YouthConnekt Mali comme chapitre national.'],
            ['2025', 'Premières formations', "Premiers ateliers et programmes d'accompagnement."],
            ['2026', 'La plateforme en ligne', 'Un espace pour relier jeunes, organisations et opportunités.'],
            ['Demain', 'Toutes les régions', 'Aller à la rencontre des jeunes partout au Mali.'],
        ],
        'axes' => axes(),
        'mot' => ($mot['publie'] && trim((string) $mot['message']) !== '') ? $mot : null,
        'temoignages' => lignes('SELECT * FROM temoignages WHERE publie = 1 ORDER BY ordre, id DESC LIMIT 3'),
    ]));
}

function contact(): void
{
    $valeurs = ['nom' => '', 'email' => '', 'profil' => 'jeune', 'sujet' => '', 'message' => '', 'consentement' => ''];
    $erreurs = [];
    $sujet = get('sujet');
    if (isset(SUJETS_CONTACT[$sujet])) {
        $valeurs['sujet'] = $sujet;
    }

    if (est_post()) {
        foreach (array_keys($valeurs) as $c) {
            $valeurs[$c] = post($c);
        }
        if ($valeurs['nom'] === '') {
            $erreurs['nom'] = 'Indiquez votre nom.';
        }
        if ($valeurs['email'] === '') {
            $erreurs['email'] = 'Indiquez votre adresse e-mail.';
        } elseif (!filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = "Cette adresse e-mail n'est pas valide.";
        }
        if (!isset(PROFILS_CONTACT[$valeurs['profil']])) {
            $erreurs['profil'] = 'Choisissez une valeur dans la liste.';
        }
        if (!isset(SUJETS_CONTACT[$valeurs['sujet']])) {
            $erreurs['sujet'] = 'Choisissez un sujet.';
        }
        if ($valeurs['message'] === '') {
            $erreurs['message'] = 'Écrivez votre message.';
        } elseif (mb_strlen($valeurs['message']) < 10) {
            $erreurs['message'] = 'Votre message est trop court.';
        }
        if ($valeurs['consentement'] === '') {
            $erreurs['consentement'] = 'Cochez cette case pour que nous puissions vous répondre.';
        }
        if (!$erreurs) {
            // Champ piège : un robot le remplit, un humain le laisse vide
            if (post('site_web') === '') {
                inserer('messages_contact', [
                    'nom' => mb_substr($valeurs['nom'], 0, 120),
                    'profil' => $valeurs['profil'],
                    'email' => mb_substr($valeurs['email'], 0, 190),
                    'objet' => SUJETS_CONTACT[$valeurs['sujet']],
                    'message' => mb_substr($valeurs['message'], 0, 5000),
                    'recu_le' => maintenant(),
                ]);
                $copie = (string) config('email.copie_contact', '');
                if ($copie !== '') {
                    envoyer_email($copie, 'Nouveau message : ' . SUJETS_CONTACT[$valeurs['sujet']],
                        "De : {$valeurs['nom']} <{$valeurs['email']}>\nProfil : " . PROFILS_CONTACT[$valeurs['profil']] . "\n\n" . $valeurs['message'],
                        '', $valeurs['email']);
                }
            }
            flash('succes', "Merci, votre message a bien été envoyé. L'équipe vous répondra par e-mail.");
            rediriger(url('/contact'));
        }
    }

    afficher(vue('pages/contact', [
        'titre' => 'Contact | YouthConnekt Mali',
        'p' => parametres(),
        'valeurs' => $valeurs,
        'erreurs' => $erreurs,
    ]));
}

function confidentialite(): void
{
    afficher(vue('pages/confidentialite', ['titre' => 'Politique de confidentialité | YouthConnekt Mali', 'p' => parametres()]));
}

function mentions_legales(): void
{
    afficher(vue('pages/mentions_legales', ['titre' => 'Mentions légales | YouthConnekt Mali', 'p' => parametres()]));
}

function newsletter(): void
{
    $email = mb_strtolower(post('email'));
    if ($email === '') {
        flash('erreur', 'Indiquez votre adresse e-mail.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
        flash('erreur', "Cette adresse e-mail n'est pas valide.");
    } else {
        if (post('site_web') === '') {
            requete('INSERT INTO abonnes_newsletter (email, inscrit_le, actif) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE actif = 1', [$email, maintenant()]);
        }
        // Même message dans tous les cas, sans révéler si l'adresse était déjà inscrite
        flash('succes', 'Merci, votre inscription à la newsletter est bien prise en compte.');
    }
    $retour = strtok(adresse_retour(url('/')), '#');
    rediriger($retour . '#newsletter');
}

/**
 * Message envoyé depuis la bulle en bas à droite (question, suggestion, situation…).
 * Répond en JSON quand il est envoyé par le script de la page, sinon revient à la page d'origine.
 */
function message_rapide(): void
{
    $ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
    $u = utilisateur();
    $nom = $u ? $u['nom_affiche'] : post('nom');
    $email = $u ? $u['email'] : mb_strtolower(post('email'));
    $message = post('message');
    $erreurs = [];
    if ($nom === '') {
        $erreurs['nom'] = 'Indiquez votre nom.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = $email === '' ? 'Indiquez votre adresse e-mail pour recevoir la réponse.' : "Cette adresse e-mail n'est pas valide.";
    }
    if (mb_strlen($message) < 5) {
        $erreurs['message'] = $message === '' ? 'Écrivez votre message.' : 'Votre message est trop court.';
    }
    if (!$erreurs && post('site_web') === '') { // champ piège contre les robots
        inserer('messages_contact', [
            'nom' => mb_substr($nom, 0, 120),
            'profil' => $u ? ($u['type_compte'] === 'organisation' ? 'organisation' : 'jeune') : 'autre',
            'email' => mb_substr($email, 0, 190),
            'objet' => 'Message envoyé depuis la bulle du site',
            'message' => mb_substr($message, 0, 3000),
            'recu_le' => maintenant(),
        ]);
        $copie = (string) config('email.copie_contact', '');
        if ($copie !== '') {
            envoyer_email($copie, 'Nouveau message depuis la bulle du site', "De : $nom <$email>\n\n$message", '', $email);
        }
    }
    $texte = 'Merci, votre message a bien été envoyé. L\'équipe vous répondra par e-mail.';
    if ($ajax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($erreurs ? ['ok' => false, 'erreurs' => $erreurs] : ['ok' => true, 'message' => $texte], JSON_UNESCAPED_UNICODE);
        exit;
    }
    flash($erreurs ? 'erreur' : 'succes', $erreurs ? reset($erreurs) : $texte);
    rediriger(adresse_retour(url('/')));
}

