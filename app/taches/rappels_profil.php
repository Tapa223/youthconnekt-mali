#!/usr/local/bin/php -q
<?php
/*
 * Rappel par e-mail aux membres qui n'ont pas encore complété leur profil après l'inscription courte.
 *
 * Lancé une fois par jour par une tâche planifiée de cPanel (Tâches Cron) :
 *   /usr/local/bin/php /home/IDENTIFIANT/public_html/app/taches/rappels_profil.php
 *
 * Règles : au plus 2 rappels par membre (2 jours puis 7 jours après l'inscription),
 * jamais deux rappels à moins de 5 jours d'écart, au plus 60 e-mails par passage
 * (pour rester sous la limite quotidienne de Brevo, partagée avec les autres e-mails du site).
 * Ce script ne s'exécute qu'en ligne de commande, jamais depuis un navigateur (le dossier app/ est fermé au web).
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$racine = dirname(__DIR__, 2);
require $racine . '/app/noyau.php';
require $racine . '/app/auth.php';
require $racine . '/app/email.php';
require $racine . '/app/contenus.php';

const RAPPELS_MAX = 2;
const RAPPELS_PAR_PASSAGE = 60;

$manque_individuel = "(p.genre = '' OR p.tranche_age = '' OR p.categorie = '' OR p.region = '' OR (p.categorie = 'autre' AND p.categorie_autre = ''))";
$manque_organisation = "(o.region = '' OR o.personne_contact = '')";

$membres = lignes(
    "SELECT u.id, u.email, u.type_compte, u.rappel_profil_nb, COALESCE(p.prenom, o.nom_structure) AS prenom
     FROM utilisateurs u
     LEFT JOIN profils_individuels p ON p.utilisateur_id = u.id AND u.type_compte = 'individuel'
     LEFT JOIN profils_organisations o ON o.utilisateur_id = u.id AND u.type_compte = 'organisation'
     WHERE u.actif = 1 AND u.type_compte IN ('individuel', 'organisation')
       AND u.rappel_profil_nb < ?
       AND u.inscrit_le <= NOW() - INTERVAL IF(u.rappel_profil_nb = 0, 2, 7) DAY
       AND (u.rappel_profil_le IS NULL OR u.rappel_profil_le <= NOW() - INTERVAL 5 DAY)
       AND ((u.type_compte = 'individuel' AND p.id IS NOT NULL AND $manque_individuel)
         OR (u.type_compte = 'organisation' AND o.id IS NOT NULL AND $manque_organisation))
     ORDER BY u.inscrit_le
     LIMIT " . RAPPELS_PAR_PASSAGE,
    [RAPPELS_MAX]
);

$lien = url_absolue(url('/compte/mon-espace')) . '#completer';
$envoyes = 0;
foreach ($membres as $m) {
    $dernier = (int) $m['rappel_profil_nb'] + 1 >= RAPPELS_MAX;
    $bonjour = trim((string) $m['prenom']) !== '' ? 'Bonjour ' . trim((string) $m['prenom']) . ',' : 'Bonjour,';
    $texte = $bonjour . "\n\n"
        . ($m['type_compte'] === 'organisation'
            ? "Votre compte YouthConnekt Mali est bien créé. Il reste deux informations à indiquer (la région et la personne de contact) pour que l'équipe du chapitre puisse vous mettre en relation avec les jeunes et les bons partenaires."
            : "Votre compte YouthConnekt Mali est bien créé. Il reste quelques questions rapides (votre tranche d'âge, votre région, ce que vous faites) pour vous proposer les opportunités et les communautés qui vous correspondent.")
        . "\n\nCela prend moins d'une minute, depuis votre espace :"
        . ($dernier ? "\n\nC'est notre dernier rappel à ce sujet : nous ne vous écrirons plus pour cela." : '')
        . "\n\nÀ très vite,\nL'équipe YouthConnekt Mali";
    $ok = envoyer_email($m['email'], 'Votre profil YouthConnekt Mali : encore une minute', $texte, $lien, coordonnees_email()['email'], 'Rappel de profil');
    if ($ok) {
        requete('UPDATE utilisateurs SET rappel_profil_nb = rappel_profil_nb + 1, rappel_profil_le = NOW() WHERE id = ?', [$m['id']]);
        $envoyes++;
    }
}
journaliser('taches.log', 'Rappels de profil : ' . $envoyes . ' envoyé(s) sur ' . count($membres) . ' membre(s) concerné(s).');
