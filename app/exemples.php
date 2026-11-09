<?php
/*
 * Contenus d'exemple des maquettes, pour voir les pages remplies pendant le développement.
 * Ils sont reconnaissables à leur adresse, qui commence par « exemple- ».
 * Ils ne doivent jamais rester en ligne : ce ne sont pas des informations officielles.
 * Administration > Paramètres > Contenus d'exemple : « Charger » ou « Retirer ».
 */

declare(strict_types=1);

const EXEMPLES_OPPORTUNITES = [
    ['stage-en-gestion-de-projet', 'Stage en gestion de projet', 'emploi', 19, '', 'Bamako', 'stand', ['jeunes-professionnels']],
    ['atelier-monter-son-projet', 'Atelier « Monter son projet »', 'formation', 24, 'YouthConnekt Mali', 'Ségou · présentiel', 'salle-bleue', ['entrepreneuriat-et-startups']],
    ['stage-en-communication-numerique', 'Stage en communication numérique', 'emploi', 32, '', 'Bamako', 'panel', ['tech-et-numerique']],
    ['formation-en-marketing-digital', 'Formation en marketing digital', 'formation', 34, 'YouthConnekt Mali', 'Bamako, présentiel', 'stand', ['tech-et-numerique', 'entrepreneuriat-et-startups']],
    ['bourse-detudes-en-entrepreneuriat', "Bourse d'études en entrepreneuriat", 'bourse', 39, '', 'International', 'ceremonie', ['etudiants']],
    ['concours-de-pitch-jeunes-innovateurs', 'Concours de pitch « Jeunes innovateurs »', 'evenement', 42, 'YouthConnekt Mali', 'Bamako', 'ceremonie', ['innovation-et-porteurs-de-projets', 'agriculture-et-environnement']],
    ['programme-de-mentorat-2027', 'Programme de mentorat 2027', 'appel', 49, 'YouthConnekt Mali et partenaires', 'En ligne', 'intervenant', ['leadership-et-citoyennete']],
    ['forum-national-de-lemploi-des-jeunes', "Forum national de l'emploi des jeunes", 'evenement', 75, '', 'Bamako · présentiel', 'arena', ['jeunes-professionnels']],
    ['bourse-de-leadership-feminin', 'Bourse de leadership féminin', 'bourse', 85, '', 'En ligne', 'intervenant', ['leadership-et-citoyennete']],
];

const EXEMPLE_DETAIL_MARKETING = [
    'description' => "Cette formation pratique apprend à promouvoir un projet, une association ou une petite entreprise sur internet : réseaux sociaux, contenus, publicité en ligne et mesure des résultats.\n\nElle est animée par des professionnels du numérique et se termine par la présentation d'un plan de communication.",
    'avantages' => "Des méthodes concrètes, applicables tout de suite\nUn accompagnement personnalisé sur votre projet\nUne attestation de participation\nUn réseau de jeunes et de professionnels",
    'criteres' => "Avoir entre 18 et 35 ans\nPorter un projet ou une activité, même débutante\nÊtre disponible deux soirs par semaine",
    'etapes' => "Remplissez le formulaire de candidature en ligne.\nJoignez une courte présentation de votre projet.\nLes candidats retenus sont contactés par e-mail.",
    'duree' => '4 semaines, 2 soirs par semaine', 'public' => 'Jeunes de 18 à 35 ans', 'cout' => 'Gratuit',
];

// [slug, titre, catégorie, décalage en jours de la date de l'événement, lieu, photo, chapeau]
function photo_exemple(string $nom): string
{
    $dossier = RACINE . '/uploads/exemples';
    if (!is_dir($dossier)) {
        @mkdir($dossier, 0775, true);
    }
    $cible = 'uploads/exemples/exemple-' . $nom . '.jpg';
    if (!is_file(RACINE . '/' . $cible)) {
        @copy(RACINE . '/assets/img/exemples/' . $nom . '.jpg', RACINE . '/' . $cible);
    }
    return $cible;
}

function charger_exemples(): void
{
    $maintenant = time();
    $axes = [];
    foreach (lignes('SELECT id, slug FROM axes') as $a) {
        $axes[$a['slug']] = (int) $a['id'];
    }
    $communautes = [];
    foreach (lignes('SELECT id, slug FROM communautes') as $c) {
        $communautes[$c['slug']] = (int) $c['id'];
    }

    foreach (EXEMPLES_OPPORTUNITES as $ordre => [$s, $titre, $type, $jours, $organisme, $lieu, $image, $coms]) {
        if ($type !== 'formation') {
            continue; // seules les formations d'exemple sont gardées : le reste du site ne montre que des contenus réels
        }
        $slug = 'exemple-' . $s;
        if (valeur('SELECT id FROM opportunites WHERE slug = ?', [$slug])) {
            continue;
        }
        $donnees = [
            'titre' => $titre, 'slug' => $slug, 'type' => $type, 'organisme' => $organisme, 'lieu' => $lieu, 'statut' => 'publie',
            'resume' => $titre . ' relayée par le chapitre.', 'description' => "Description complète à fournir par l'organisateur.",
            'date_limite' => date('Y-m-d', strtotime('+' . $jours . ' days')), 'mis_en_avant' => in_array($ordre, [3, 6, 7], true) ? 1 : 0,
            'publie_le' => date('Y-m-d H:i:s', $maintenant - ($ordre + 1) * 86400), 'image' => photo_exemple($image),
            'cree_le' => maintenant(), 'modifie_le' => maintenant(),
        ];
        if ($s === 'formation-en-marketing-digital') {
            $donnees = array_merge($donnees, EXEMPLE_DETAIL_MARKETING);
        }
        $id = inserer('opportunites', $donnees);
        foreach ($coms as $c) {
            if (isset($communautes[$c])) {
                inserer('opportunite_communaute', ['opportunite_id' => $id, 'communaute_id' => $communautes[$c]]);
            }
        }
    }

    requete(
        "UPDATE communautes SET description = ?, activites = ? WHERE slug = 'agriculture-et-environnement' AND (description IS NULL OR description = '')",
        [
            "Jeunes agriculteurs, transformateurs, étudiants et acteurs du climat qui veulent faire de l'agriculture un métier d'avenir.",
            "Ateliers pratiques sur la transformation et la conservation\nVisites de fermes et d'unités de transformation\nPartage d'opportunités de financement agricole",
        ]
    );
}

function retirer_exemples(): int
{
    $total = 0;
    foreach (['initiatives', 'opportunites', 'actualites'] as $table) {
        $total += requete("DELETE FROM `$table` WHERE slug LIKE 'exemple-%'")->rowCount();
    }
    requete("UPDATE communautes SET description = '', activites = '' WHERE slug = 'agriculture-et-environnement' AND description LIKE 'Jeunes agriculteurs, transformateurs, étudiants%'");
    foreach (glob(RACINE . '/uploads/exemples/exemple-*.jpg') ?: [] as $f) {
        @unlink($f);
    }
    return $total;
}

function nombre_exemples(): int
{
    $n = 0;
    foreach (['initiatives', 'opportunites', 'actualites'] as $table) {
        $n += (int) valeur("SELECT COUNT(*) FROM `$table` WHERE slug LIKE 'exemple-%'");
    }
    return $n;
}
