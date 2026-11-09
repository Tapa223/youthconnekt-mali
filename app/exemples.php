<?php
/*
 * Contenus d'exemple des maquettes, pour voir les pages remplies pendant le développement.
 * Ils sont reconnaissables à leur adresse, qui commence par « exemple- ».
 * Ils ne doivent jamais rester en ligne : ce ne sont pas des informations officielles.
 * Administration > Paramètres > Contenus d'exemple : « Charger » ou « Retirer ».
 */

declare(strict_types=1);

const EXEMPLES_INITIATIVES = [
    ['slug' => 'bootcamp-jeunes-entrepreneurs', 'titre' => 'Bootcamp Jeunes Entrepreneurs', 'axe' => 'croissance-environnement', 'photo' => 'stand',
     'resume' => 'Trois jours pour transformer une idée en projet, avec des mentors, des ateliers pratiques et une présentation finale devant un jury.',
     'contenu' => "Beaucoup de jeunes ont une idée de projet mais ne savent pas par où commencer. Le Bootcamp Jeunes Entrepreneurs leur donne trois jours pour passer de l'idée à un projet solide, accompagnés par des entrepreneurs qui sont passés par là.\n\nLes participants travaillent en petites équipes, alternent ateliers et séances de mentorat, puis présentent leur projet devant un jury composé de partenaires du chapitre.",
     'etat' => 'terminee', 'date_libelle' => '12 au 14 mars 2026', 'lieu' => 'Bamako', 'participants' => '60 jeunes', 'format' => 'Présentiel',
     'objectifs' => "Transformer une idée en projet structuré\nRencontrer des entrepreneurs et des mentors\nPrésenter son projet devant un jury\nRepartir avec un plan d'action concret",
     'programme' => "Jour 1 | Trouver et tester son idée | Ateliers sur le problème à résoudre, le client et la valeur du projet.\nJour 2 | Construire son modèle | Coûts, revenus, partenaires : chaque équipe bâtit son modèle avec un mentor.\nJour 3 | Convaincre | Préparation du pitch et présentation finale devant le jury.",
     'chiffres' => "60 | jeunes accompagnés\n12 | mentors mobilisés\n15 | projets présentés",
     'citation' => "J'y suis arrivée avec une idée, je suis repartie avec un projet et des mentors.", 'citation_auteur' => 'Une participante',
     'galerie' => ['salle-bleue', 'intervenant', 'panel', 'arena'], 'une' => 1],
    ['slug' => 'caravane-de-la-citoyennete', 'titre' => 'Caravane de la citoyenneté', 'axe' => 'gouvernance-participation', 'photo' => 'panel',
     'resume' => 'À la rencontre des jeunes dans les régions pour parler engagement.', 'contenu' => 'La caravane va à la rencontre des jeunes dans les régions pour parler engagement citoyen.',
     'etat' => 'terminee', 'date_libelle' => 'Janvier 2026', 'lieu' => 'Ségou, Mopti'],
    ['slug' => 'prix-des-jeunes-innovateurs', 'titre' => 'Prix des jeunes innovateurs', 'axe' => 'croissance-environnement', 'photo' => 'arena',
     'resume' => 'Récompenser les projets qui font bouger le pays.', 'contenu' => 'Un prix pour mettre en lumière les projets innovants portés par des jeunes.',
     'etat' => 'terminee', 'date_libelle' => 'Décembre 2025', 'lieu' => 'Bamako'],
    ['slug' => 'atelier-leadership-des-jeunes-femmes', 'titre' => 'Atelier leadership des jeunes femmes', 'axe' => 'croissance-environnement', 'photo' => 'intervenant',
     'resume' => 'Prise de parole, confiance et réseau pour les jeunes femmes.', 'contenu' => 'Un atelier pour renforcer la prise de parole, la confiance et le réseau des jeunes femmes.',
     'etat' => 'terminee', 'date_libelle' => 'Novembre 2025', 'lieu' => 'Bamako'],
    ['slug' => 'dialogue-jeunes-et-decideurs', 'titre' => 'Dialogue jeunes et décideurs', 'axe' => 'gouvernance-participation', 'photo' => 'ceremonie',
     'resume' => "Une journée d'échanges entre jeunes et institutions.", 'contenu' => "Une journée d'échanges directs entre jeunes et institutions.",
     'etat' => 'terminee', 'date_libelle' => 'Octobre 2025', 'lieu' => 'Bamako'],
    ['slug' => 'vitrine-des-talents', 'titre' => 'Vitrine des talents', 'axe' => 'croissance-environnement', 'photo' => 'salle-bleue',
     'resume' => 'Mettre en lumière les jeunes créateurs et artisans.', 'contenu' => 'Une vitrine pour mettre en lumière les jeunes créateurs et artisans.',
     'etat' => 'terminee', 'date_libelle' => 'Septembre 2025', 'lieu' => 'Sikasso'],
];

// [slug, titre, type, jours avant la date limite, organisme, lieu, photo, communautés]
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
const EXEMPLES_ACTUALITES = [
    ['participation-youthconnekt-sahel', 'Participation du Mali au YouthConnekt Sahel', 'evenement', 40, '', 'ceremonie', 'Une délégation de jeunes Maliens au forum régional de la jeunesse sahélienne.'],
    ['lancement-officiel-de-la-plateforme', 'Lancement officiel de la plateforme YouthConnekt Mali', 'actualite', null, '', 'ceremonie', "Une nouvelle porte d'entrée pour les jeunes et les organisations du pays."],
    ['atelier-monter-son-projet-segou', 'Atelier « Monter son projet »', 'evenement', 46, 'Ségou', 'salle-bleue', 'Un atelier pratique pour structurer son projet.'],
    ['forum-national-emploi', "Forum national de l'emploi des jeunes", 'evenement', 69, 'Bamako', 'arena', 'Rencontrer des recruteurs et découvrir des offres.'],
    ['retour-atelier-leadership', 'Atelier leadership des jeunes femmes', 'evenement', -4, 'Bamako', 'intervenant', "Trois jours d'échanges, de prise de parole et de réseau."],
    ['caravane-a-segou', 'La Caravane de la citoyenneté à Ségou', 'evenement', -26, 'Ségou', 'panel', 'Ce que les jeunes nous ont dit pendant la Caravane.'],
    ['remise-prix-innovateurs', 'Remise du Prix des jeunes innovateurs', 'evenement', -49, 'Bamako', 'stand', "Les lauréats de l'édition ont été récompensés."],
    ['appel-a-benevoles-forum', 'Appel à bénévoles pour le forum national', 'actualite', null, '', 'salle-bleue', "Le chapitre recherche des bénévoles pour l'organisation du forum."],
    ['cinq-jeunes-agriculture', "Cinq jeunes qui innovent dans l'agriculture", 'actualite', null, '', 'stand', 'Portraits de jeunes qui réinventent les filières agricoles.'],
    ['preparer-une-candidature', 'Comment préparer une candidature solide', 'actualite', null, '', 'arena', 'Nos conseils pour réussir vos dossiers de candidature.'],
    ['retour-leadership-femmes', "Retour sur l'atelier leadership des jeunes femmes", 'actualite', null, '', 'intervenant', "Trois jours d'échanges, de prise de parole et de réseau."],
    ['rencontre-jeunes-segou', 'Le chapitre à la rencontre des jeunes de Ségou', 'actualite', null, '', 'panel', 'Ce que les jeunes nous ont dit pendant la Caravane.'],
    ['formations-numerique', 'Nouvelles opportunités de formation au numérique', 'actualite', null, '', 'ceremonie', 'Le point sur les formations ouvertes ce trimestre.'],
];

const EXEMPLE_CORPS_LANCEMENT = "La plateforme YouthConnekt Mali a été présentée officiellement à Bamako, devant des jeunes venus de plusieurs régions, des représentants d'organisations et des partenaires du chapitre.

Pensée comme une porte d'entrée unique, elle permet de découvrir les opportunités du moment, de suivre la vie du chapitre et de rejoindre la communauté, à titre individuel ou en tant qu'organisation.

> Aujourd'hui, on n'a plus d'excuse : les opportunités sont là, il suffit de se connecter.
— Une participante

## Ce que la plateforme apporte

- Des opportunités mises à jour chaque semaine
- Un espace membre pour suivre ses communautés
- Une communauté de jeunes et d'organisations dans tout le pays

[photos]

Au programme de la journée : une présentation de la plateforme, des témoignages de jeunes accompagnés par le chapitre et des ateliers pratiques pour créer son profil et repérer les bonnes opportunités.

Article d'exemple, photos provisoires prises hors du Mali.";

/** Copie une photo d'exemple dans uploads/exemples/ et renvoie son chemin. */
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

    foreach (EXEMPLES_INITIATIVES as $i => $d) {
        $slug = 'exemple-' . $d['slug'];
        if (valeur('SELECT id FROM initiatives WHERE slug = ?', [$slug])) {
            continue;
        }
        $id = inserer('initiatives', [
            'titre' => $d['titre'], 'slug' => $slug, 'statut' => 'publie',
            'publie_le' => date('Y-m-d H:i:s', $maintenant - $i * 86400), 'mis_en_avant' => $d['une'] ?? 0,
            'axe_id' => $axes[$d['axe']] ?? null, 'resume' => $d['resume'], 'contenu' => $d['contenu'],
            'image' => photo_exemple($d['photo']), 'etat' => $d['etat'], 'date_libelle' => $d['date_libelle'],
            'lieu' => $d['lieu'], 'participants' => $d['participants'] ?? '', 'format' => $d['format'] ?? '',
            'objectifs' => $d['objectifs'] ?? '', 'programme' => $d['programme'] ?? '', 'chiffres' => $d['chiffres'] ?? '',
            'citation' => $d['citation'] ?? '', 'citation_auteur' => $d['citation_auteur'] ?? '',
            'cree_le' => maintenant(), 'modifie_le' => maintenant(),
        ]);
        foreach ($d['galerie'] ?? [] as $ordre => $nom) {
            inserer('photos_initiatives', ['initiative_id' => $id, 'image' => photo_exemple($nom), 'legende' => '', 'ordre' => $ordre]);
        }
    }

    foreach (EXEMPLES_OPPORTUNITES as $ordre => [$s, $titre, $type, $jours, $organisme, $lieu, $image, $coms]) {
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

    foreach (EXEMPLES_ACTUALITES as $ordre => [$s, $titre, $categorie, $decalage, $lieu, $image, $chapeau]) {
        $slug = 'exemple-' . $s;
        if (valeur('SELECT id FROM actualites WHERE slug = ?', [$slug])) {
            continue;
        }
        $id = inserer('actualites', [
            'titre' => $titre, 'slug' => $slug, 'categorie' => $categorie, 'chapeau' => $chapeau, 'lieu' => $lieu, 'statut' => 'publie',
            'contenu' => $s === 'lancement-officiel-de-la-plateforme' ? EXEMPLE_CORPS_LANCEMENT : $chapeau,
            'date_evenement' => $decalage !== null ? date('Y-m-d', strtotime(($decalage >= 0 ? '+' : '') . $decalage . ' days')) : null,
            'mis_en_avant' => $ordre === 0 ? 1 : 0, 'publie_le' => date('Y-m-d H:i:s', $maintenant - $ordre * 3 * 86400),
            'image' => photo_exemple($image), 'cree_le' => maintenant(), 'modifie_le' => maintenant(),
        ]);
        if ($s === 'lancement-officiel-de-la-plateforme') {
            foreach (['intervenant', 'salle-bleue'] as $o => $nom) {
                inserer('photos_actualites', ['actualite_id' => $id, 'image' => photo_exemple($nom), 'legende' => '', 'ordre' => $o]);
            }
        }
        if ($s === 'cinq-jeunes-agriculture' && isset($communautes['agriculture-et-environnement'])) {
            inserer('actualite_communaute', ['actualite_id' => $id, 'communaute_id' => $communautes['agriculture-et-environnement']]);
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
