<?php
/*
 * Rubriques de l'administration, décrites une fois pour toutes :
 * ce que montre la liste (colonnes, onglets, filtres, recherche, export),
 * ce que contient le formulaire (champs groupés en cartes) et ce qui se passe à l'enregistrement.
 * Le moteur qui les affiche est dans app/controleurs/admin.php.
 */

declare(strict_types=1);

/** Champs de publication communs aux contenus (panneau de droite). */
function groupe_publication(bool $propositions = false, ?array $mise_en_avant = null): array
{
    $statuts = ['brouillon' => 'Brouillon', 'publie' => 'Publié'];
    if ($propositions) {
        $statuts = ['propose' => 'À valider (proposée par un membre)'] + $statuts;
    }
    return ['Publication', 'cote' => true, 'champs' => [
        'statut' => ['type' => 'select', 'libelle' => 'Statut', 'options' => $statuts, 'defaut' => 'brouillon'],
        'publie_le' => ['type' => 'datetime', 'libelle' => 'Date de publication', 'requis' => false, 'aide' => 'Une date future programme la publication. Vide : maintenant.', 'defaut' => 'maintenant'],
        'mis_en_avant' => $mise_en_avant ?? ['type' => 'case', 'libelle' => "Mettre en avant sur l'accueil"],
        'slug' => ['type' => 'slug', 'libelle' => 'Adresse (URL)', 'requis' => false, 'max' => 200, 'aide' => 'Laisser vide : elle est créée à partir du titre.'],
    ]];
}

/** Badge de statut d'un contenu publiable. */
function badge_statut(array $l, bool $feminin = false): string
{
    if ($l['statut'] === 'publie') {
        return strtotime($l['publie_le']) > time() ? badge('Programmé' . ($feminin ? 'e' : ''), 'bleu') : badge('Publié' . ($feminin ? 'e' : ''), 'vert');
    }
    if ($l['statut'] === 'propose') {
        return badge('À valider', 'orange');
    }
    return badge('Brouillon', 'gris');
}

function actions_publication(string $table): array
{
    $une = $table === 'actualites';
    return [
        'publier' => ['Publier', fn ($ids) => requete("UPDATE `$table` SET statut = 'publie' WHERE id IN (" . marqueurs($ids) . ')', $ids)->rowCount() . ' élément(s) publié(s).'],
        'depublier' => ['Repasser en brouillon', fn ($ids) => requete("UPDATE `$table` SET statut = 'brouillon' WHERE id IN (" . marqueurs($ids) . ')', $ids)->rowCount() . ' élément(s) repassé(s) en brouillon.'],
        'avant' => [$une ? 'Mettre à la une' : "Mettre en avant sur l'accueil", fn ($ids) => requete("UPDATE `$table` SET mis_en_avant = 1 WHERE id IN (" . marqueurs($ids) . ')', $ids)->rowCount() . ' élément(s) mis en avant.'],
        'retirer' => [$une ? 'Retirer de la une' : "Retirer de l'accueil", fn ($ids) => requete("UPDATE `$table` SET mis_en_avant = 0 WHERE id IN (" . marqueurs($ids) . ')', $ids)->rowCount() . " élément(s) retiré(s) de l'accueil."],
    ];
}

/** Avant l'enregistrement d'un contenu publiable : adresse et dates. */
function preparer_publiable(string $table, string $champ_titre = 'titre'): callable
{
    return function (array &$d, ?array $ancien) use ($table, $champ_titre) {
        $d['slug'] = slugifier($d['slug'] ?? '') ?: slug_unique($table, $d[$champ_titre], $ancien['id'] ?? null);
        if (!$ancien || $d['slug'] !== $ancien['slug']) {
            $d['slug'] = slug_unique($table, $d['slug'], $ancien['id'] ?? null);
        }
        if (array_key_exists('publie_le', $d) && !$d['publie_le']) {
            $d['publie_le'] = maintenant();
        }
        $d['modifie_le'] = maintenant();
        if (!$ancien) {
            $d['cree_le'] = maintenant();
        }
    };
}

function options_communautes(): array
{
    $o = [];
    foreach (lignes('SELECT id, nom, active FROM communautes ORDER BY ordre, nom') as $c) {
        $o[$c['id']] = $c['nom'] . ($c['active'] ? '' : ' (désactivée)');
    }
    return $o;
}

function options_axes(): array
{
    $o = ['' => 'Aucun'];
    foreach (axes() as $a) {
        $o[$a['id']] = $a['nom'];
    }
    return $o;
}

const AIDE_MISE_EN_FORME = 'Écrivez ou collez votre texte : chaque retour à la ligne crée un paragraphe. Les lignes qui commencent par un tiret deviennent une liste, une ligne courte sans point final devient un intertitre.';

function ressource(string $slug): ?array
{
    $r = ressources()[$slug] ?? null;
    if ($r) {
        $r['slug'] = $slug;
        $r += [
            'rubrique' => $slug, 'description' => '', 'libelle_tous' => 'Tous', 'onglets' => [], 'filtres' => [],
            'recherche' => [], 'actions' => [], 'export' => [], 'par_page' => 25, 'peut_ajouter' => true,
            'peut_supprimer' => true, 'groupes' => [], 'vue_liste' => 'admin/liste', 'url_voir' => null,
            'aide_cote' => '', 'fiche_unique' => false, 'galerie' => null, 'liaison' => null,
            'avant' => null, 'apres' => null, 'condition' => '1=1', 'parent' => null, 'menu_liste' => [], 'ajout_discret' => false, 'sans_fiche' => false, 'decisions' => [], 'vue_fiche' => null, 'guide' => null, 'champs_select' => 't.*', 'depuis' => 'FROM `' . $r['table'] . '` t',
            'titre_ajout' => $r['libelle_ajout'] ?? 'Ajouter', 'nom_element' => fn ($l) => (string) ($l['titre'] ?? $l['nom'] ?? ''),
        ];
    }
    return $r;
}

/** Profil d'une personne qui témoigne, déduit de son compte (femme, homme, organisation, entreprise). */
const SQL_PROFIL_TEMOIGNAGE = "(CASE WHEN pi.genre IN ('femme', 'homme') THEN pi.genre
    WHEN po.type_structure IN ('entreprise', 'startup') THEN 'entreprise'
    WHEN po.id IS NOT NULL THEN 'organisation'
    ELSE NULLIF(t.categorie, '') END)";

function ressources(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $aujourdhui = aujourdhui();
    $liste = [

        // ------------------------------------------------------------------
        'opportunites' => [
            'table' => 'opportunites', 'titre' => 'Opportunités',
            'description' => 'Gérez les opportunités publiées sur le site.',
            'libelle_ajout' => 'Ajouter une opportunité', 'libelle_tous' => 'Toutes',
            'ordre' => 't.publie_le DESC',
            'onglets' => [
                ['À valider', "t.statut = 'propose'"],
                ['Publiées', "t.statut = 'publie'"],
                ['Brouillons', "t.statut = 'brouillon'"],
                ['Clôturées', 't.date_limite < CURDATE()'],
            ],
            'champs_select' => "t.*, COALESCE(CONCAT(pi.prenom, ' ', pi.nom), po.nom_structure, u.email) AS propose_par_nom, u.email AS propose_par_email",
            'depuis' => 'FROM opportunites t LEFT JOIN utilisateurs u ON u.id = t.propose_par LEFT JOIN profils_individuels pi ON pi.utilisateur_id = u.id LEFT JOIN profils_organisations po ON po.utilisateur_id = u.id',
            'filtres' => [['type', 'Tous les types', options_types_opportunite(), 't.type']],
            'recherche' => ['t.titre', 't.organisme', 't.resume', 't.description', 't.lieu'],
            'colonnes' => [
                ['Opportunité', fn ($l) => nom_et_detail($l['titre'], $l['propose_par'] ? 'Proposée par ' . ($l['propose_par_nom'] ?? '') : $l['organisme']), 'lien' => true],
                ['Type', fn ($l) => e(options_types_opportunite()[$l['type']] ?? $l['type'])],
                ['Date limite', fn ($l) => e(date_chiffres($l['date_limite']))],
                ['Restant', function ($l) {
                    $j = jours_restants($l['date_limite']);
                    if ($j === null || $j < 0) return '<span class="ycm-jours">—</span>';
                    return '<span class="ycm-jours' . ($j <= 25 ? ' ycm-jours--urgent' : '') . '">J-' . $j . '</span>';
                }],
                ['Statut', fn ($l) => ($l['statut'] === 'publie' && $l['date_limite'] && $l['date_limite'] < aujourdhui()) ? badge('Clôturée', 'rouge') : badge_statut($l, true)],
            ],
            'url_voir' => fn ($l) => url('/opportunites/' . $l['slug']),
            'actions' => actions_publication('opportunites'),
            'export' => ['Titre' => 'titre', 'Organisme' => 'organisme', 'Type' => fn ($l) => options_types_opportunite()[$l['type']] ?? $l['type'],
                'Date limite' => fn ($l) => date_chiffres($l['date_limite']), 'Lieu' => 'lieu', 'Statut' => 'statut', 'Lien de candidature' => 'lien_candidature'],
            'aide_cote' => 'Une opportunité passe automatiquement en « clôturée » après sa date limite.',
            'groupes' => [
                ['Informations principales', 'champs' => [
                    'titre' => ['type' => 'texte', 'libelle' => 'Titre', 'max' => 200],
                    'type' => ['type' => 'select', 'libelle' => 'Type', 'options' => options_types_opportunite(), 'largeur' => 'demi', 'aide' => "Liste gérée dans « Types d'opportunités »."],
                    'organisme' => ['type' => 'texte', 'libelle' => 'Organisme', 'requis' => false, 'max' => 150, 'largeur' => 'demi'],
                    'date_limite' => ['type' => 'date', 'libelle' => 'Date limite', 'requis' => false, 'largeur' => 'demi'],
                    'lieu' => ['type' => 'texte', 'libelle' => 'Lieu', 'requis' => false, 'max' => 120, 'largeur' => 'demi', 'aide' => 'Exemple : Bamako, présentiel'],
                    'duree' => ['type' => 'texte', 'libelle' => 'Durée', 'requis' => false, 'max' => 120, 'largeur' => 'tiers'],
                    'public' => ['type' => 'texte', 'libelle' => 'Public', 'requis' => false, 'max' => 120, 'largeur' => 'tiers'],
                    'cout' => ['type' => 'texte', 'libelle' => 'Coût', 'requis' => false, 'max' => 80, 'largeur' => 'tiers', 'aide' => 'Exemple : Gratuit'],
                    'lien_candidature' => ['type' => 'url', 'libelle' => 'Lien de candidature', 'requis' => false],
                ]],
                ['Contenu', 'champs' => [
                    'resume' => ['type' => 'texte', 'libelle' => 'Résumé', 'max' => 300, 'aide' => 'Une phrase, affichée dans les listes.'],
                    'description' => ['type' => 'zone', 'libelle' => 'Description complète', 'aide' => AIDE_MISE_EN_FORME],
                    'avantages' => ['type' => 'zone', 'libelle' => 'Ce que vous gagnez', 'requis' => false, 'aide' => 'Un élément par ligne.', 'lignes' => 4],
                    'criteres' => ['type' => 'zone', 'libelle' => 'Qui peut postuler', 'requis' => false, 'aide' => 'Un critère par ligne. À remplir uniquement si les critères sont connus.', 'lignes' => 4],
                    'etapes' => ['type' => 'zone', 'libelle' => 'Comment postuler', 'requis' => false, 'aide' => 'Une étape par ligne.', 'lignes' => 4],
                ]],
                ['Image', 'champs' => ['image' => ['type' => 'image', 'libelle' => 'Image', 'requis' => false, 'dossier' => 'opportunites']]],
                ['Ciblage', 'description' => "Les membres de ces communautés verront l'opportunité dans leur espace.", 'champs' => [
                    'communautes' => ['type' => 'communautes', 'libelle' => 'Communautés concernées', 'requis' => false],
                ]],
                groupe_publication(),
                ['Origine', 'cote' => true, 'champs' => [
                    'propose_par' => ['type' => 'lecture', 'libelle' => 'Proposée par', 'html' => function ($l) {
                        if (empty($l['propose_par'])) return "L'équipe (ajoutée dans l'administration)";
                        $m = ligne('SELECT * FROM utilisateurs WHERE id = ?', [$l['propose_par']]);
                        if (!$m) return 'Compte supprimé';
                        $m = completer_utilisateur($m);
                        return '<strong>' . e($m['nom_affiche']) . '</strong><br>' . e($m['email']) . '<br>' . e(TYPES_COMPTE[$m['type_compte']] ?? '');
                    }],
                ]],
            ],
            'liaison' => ['opportunite_communaute', 'opportunite_id'],
            'avant' => preparer_publiable('opportunites'),
        ],

        // ------------------------------------------------------------------
        'actualites' => [
            'table' => 'actualites', 'titre' => 'Actualités et événements',
            'description' => 'Publiez des actualités et annoncez les événements du chapitre.',
            'libelle_ajout' => 'Publier une actualité', 'libelle_tous' => 'Tout',
            'ordre' => 't.publie_le DESC',
            'onglets' => [
                ['À venir', 't.date_evenement >= CURDATE()'],
                ['Brouillons', "t.statut = 'brouillon'"],
            ],
            'filtres' => [['categorie', 'Toutes catégories', array_map(fn ($c) => $c['nom'], categories_actualite()), 't.categorie']],
            'recherche' => ['t.titre', 't.chapeau', 't.contenu', 't.lieu'],
            'colonnes' => [
                ['Titre', fn ($l) => '<strong>' . e($l['titre']) . '</strong>', 'lien' => true],
                ['Catégorie', fn ($l) => isset(categories_actualite()[$l['categorie']]) ? badge(categories_actualite()[$l['categorie']]['nom'], categories_actualite()[$l['categorie']]['couleur']) : '—'],
                ['Date', fn ($l) => e(date_chiffres($l['date_evenement'] ?: $l['publie_le']))],
                ['Statut', fn ($l) => badge_statut($l, true)],
                ['À la une', fn ($l) => $l['mis_en_avant'] ? 'Oui' : '—'],
            ],
            'url_voir' => fn ($l) => url('/actualites/' . $l['slug']),
            'actions' => actions_publication('actualites'),
            'export' => ['Titre' => 'titre', 'Catégorie' => fn ($l) => categories_actualite()[$l['categorie']]['nom'] ?? '',
                'Date' => fn ($l) => date_chiffres($l['date_evenement'] ?: $l['publie_le']), 'Lieu' => 'lieu', 'Statut' => 'statut'],
            'groupes' => [
                ['Informations principales', 'champs' => [
                    'titre' => ['type' => 'texte', 'libelle' => 'Titre', 'max' => 200],
                    'categorie' => ['type' => 'select', 'libelle' => 'Catégorie', 'requis' => false, 'options' => options_categories_actualite(), 'aide' => 'Facultatif. Les catégories se gèrent dans « Catégories d\'actualités ».'],
                    'date_evenement' => ['type' => 'date', 'libelle' => "Date de l'événement", 'requis' => false, 'largeur' => 'demi', 'aide' => "Pour un événement : la page l'indique « À venir » jusqu'à cette date."],
                    'lieu' => ['type' => 'texte', 'libelle' => "Lieu de l'événement", 'requis' => false, 'max' => 120, 'largeur' => 'demi'],
                    'chapeau' => ['type' => 'zone', 'libelle' => 'Chapeau (introduction)', 'max' => 400, 'lignes' => 3],
                ]],
                ['Article', 'champs' => [
                    'contenu' => ['type' => 'zone', 'libelle' => "Corps de l'article", 'aide' => AIDE_MISE_EN_FORME, 'lignes' => 14],
                ]],
                ['Image', 'champs' => ['image' => ['type' => 'image', 'libelle' => 'Image principale', 'requis' => false, 'dossier' => 'actualites']]],
                ['Ciblage', 'description' => 'Les activités liées à une communauté apparaissent sur sa page.', 'champs' => [
                    'communautes' => ['type' => 'communautes', 'libelle' => 'Communautés concernées', 'requis' => false],
                ]],
                groupe_publication(false, ['type' => 'case', 'libelle' => 'Mettre à la une', 'aide' => "En haut de la page Actualités (quatre au plus, les plus récentes d'abord) et sur l'accueil."]),
            ],
            'galerie' => ['photos_actualites', 'actualite_id', 'actualites/galerie', 'Photothèque (défile avec l\'image principale en haut de la page, puis s\'affiche en bas)'],
            'liaison' => ['actualite_communaute', 'actualite_id'],
            'avant' => preparer_publiable('actualites'),
        ],

        // ------------------------------------------------------------------
        'initiatives' => [
            'table' => 'initiatives', 'titre' => 'Initiatives',
            'description' => 'Les programmes et actions menés par le chapitre.',
            'libelle_ajout' => 'Ajouter une initiative', 'libelle_tous' => 'Toutes',
            'champs_select' => "t.*, a.nom AS axe_nom, a.couleur AS axe_couleur,
                (SELECT COUNT(*) FROM participations p WHERE p.initiative_id = t.id) AS nb_participations,
                (SELECT COUNT(*) FROM participations p WHERE p.initiative_id = t.id AND p.statut = 'en_attente') AS nb_participations_attente",
            'depuis' => 'FROM initiatives t LEFT JOIN axes a ON a.id = t.axe_id',
            'ordre' => 't.publie_le DESC',
            'onglets' => [['À venir', "t.etat = 'a_venir'"], ['En cours', "t.etat = 'en_cours'"], ['Terminées', "t.etat = 'terminee'"], ['Participations ouvertes', 't.participations_ouvertes = 1']],
            'filtres' => [],
            'recherche' => ['t.titre', 't.resume', 't.contenu', 't.lieu', 't.date_libelle'],
            'colonnes' => [
                ['Initiative', fn ($l) => nom_et_detail($l['titre'], '', miniature($l['image'])), 'lien' => true],
                ['État', fn ($l) => e(ETATS_INITIATIVE[$l['etat']] ?? '')],
                ['Date', fn ($l) => e($l['date_libelle'] ?: '—')],
                ['Participations', fn ($l) => '<a class="ycm-lien" href="' . url('/gestion/initiatives/' . $l['id'] . '/participations') . '">' . ($l['participations_ouvertes'] ? (int) $l['nb_participations'] . ' reçue' . pluriel((int) $l['nb_participations']) : 'Configurer') . '</a>' . ($l['nb_participations_attente'] ? ' ' . badge($l['nb_participations_attente'] . ' à traiter', 'orange') : '')],
                ['Statut', fn ($l) => badge_statut($l, true)],
            ],
            'url_voir' => fn ($l) => url('/initiatives/' . $l['slug']),
            'actions' => actions_publication('initiatives'),
            'export' => ['Titre' => 'titre', 'Axe' => 'axe_nom', 'État' => fn ($l) => ETATS_INITIATIVE[$l['etat']] ?? '', 'Date' => 'date_libelle', 'Lieu' => 'lieu', 'Statut' => 'statut'],
            'groupes' => [
                ['Informations principales', 'champs' => [
                    'titre' => ['type' => 'texte', 'libelle' => 'Titre', 'max' => 200],
                    'axe_id' => ['type' => 'select', 'libelle' => "Axe d'intervention", 'options' => options_axes(), 'requis' => false, 'largeur' => 'demi', 'nul' => true, 'aide' => "Facultatif. Affiché discrètement en bas de la page de l'initiative."],
                    'etat' => ['type' => 'select', 'libelle' => 'État', 'options' => ETATS_INITIATIVE, 'largeur' => 'demi'],
                    'resume' => ['type' => 'texte', 'libelle' => 'Résumé', 'max' => 300],
                ]],
                ['Informations pratiques', 'champs' => [
                    'date_libelle' => ['type' => 'texte', 'libelle' => 'Date affichée', 'requis' => false, 'max' => 80, 'largeur' => 'demi', 'aide' => 'Exemple : 12 au 14 mars 2026'],
                    'date_debut' => ['type' => 'date', 'libelle' => 'Premier jour', 'requis' => false, 'largeur' => 'demi', 'aide' => "Facultatif. Permet aux visiteurs d'ajouter l'initiative à leur agenda."],
                    'date_fin' => ['type' => 'date', 'libelle' => 'Dernier jour', 'requis' => false, 'largeur' => 'demi', 'aide' => 'Laisser vide si elle dure un seul jour.'],
                    'lieu' => ['type' => 'texte', 'libelle' => 'Lieu', 'requis' => false, 'max' => 120, 'largeur' => 'demi'],
                    'participants' => ['type' => 'texte', 'libelle' => 'Participants', 'requis' => false, 'max' => 80, 'largeur' => 'demi', 'aide' => 'Exemple : 60 jeunes'],
                    'format' => ['type' => 'texte', 'libelle' => 'Format', 'requis' => false, 'max' => 80, 'largeur' => 'demi', 'aide' => 'Exemple : Présentiel'],
                ]],
                ['Participations', 'cote' => true, 'description' => "Les jeunes peuvent se proposer comme bénévoles, intervenants ou participants. Vous créez vous-même les rôles et les questions.", 'champs' => [
                    'participations_lien' => ['type' => 'lecture', 'libelle' => 'Formulaire et candidatures', 'html' => fn ($l) => empty($l['id']) ? "Enregistrez d'abord l'initiative." : ($l['participations_ouvertes'] ? badge('Ouvertes', 'vert') : badge('Fermées', 'gris')) . ' ' . (int) ($l['nb_participations'] ?? 0) . ' reçue' . pluriel((int) ($l['nb_participations'] ?? 0)) . '<br><a class="ycm-bouton-contour ycm-petit" style="margin-top:8px" href="' . url('/gestion/initiatives/' . $l['id'] . '/participations') . '">Gérer les participations</a>'],
                ]],
                ['Contenu', 'champs' => [
                    'contenu' => ['type' => 'zone', 'libelle' => 'Présentation détaillée', 'aide' => AIDE_MISE_EN_FORME, 'lignes' => 8],
                    'objectifs' => ['type' => 'zone', 'libelle' => 'Objectifs', 'requis' => false, 'aide' => 'Un objectif par ligne.', 'lignes' => 4],
                    'programme' => ['type' => 'zone', 'libelle' => 'Programme', 'requis' => false, 'aide' => 'Une étape par ligne, sous la forme : Jour 1 | Titre | Description', 'lignes' => 4],
                    'chiffres' => ['type' => 'zone', 'libelle' => 'En chiffres', 'requis' => false, 'aide' => 'Un chiffre vérifié par ligne, sous la forme : 60 | jeunes accompagnés', 'lignes' => 3],
                    'citation' => ['type' => 'zone', 'libelle' => 'Citation', 'requis' => false, 'max' => 400, 'lignes' => 2],
                    'citation_auteur' => ['type' => 'texte', 'libelle' => 'Auteur de la citation', 'requis' => false, 'max' => 120],
                ]],
                ['Image', 'champs' => ['image' => ['type' => 'image', 'libelle' => 'Image principale', 'requis' => false, 'dossier' => 'initiatives']]],
                groupe_publication(),
            ],
            'galerie' => ['photos_initiatives', 'initiative_id', 'initiatives/galerie', 'Galerie « En images »'],
            'avant' => preparer_publiable('initiatives'),
        ],

        // ------------------------------------------------------------------
        'temoignages' => [
            'table' => 'temoignages', 'titre' => 'Témoignages',
            'description' => "Envoyés par les membres depuis leur espace. Vous pouvez les afficher, les retirer ou les mettre en avant (ils passent alors en premier) ; leur texte ne se modifie pas.",
            'libelle_ajout' => 'Ajouter un témoignage', 'vue_liste' => 'admin/liste_temoignages',
            'ordre' => 't.publie, t.a_la_une DESC, t.ordre, t.id DESC', 'par_page' => 60, 'recherche' => ['t.nom', 't.texte', 't.role'],
            'onglets' => [
                ['À valider', 't.publie = 0 AND t.utilisateur_id IS NOT NULL'],
                ['Mis en avant', 't.publie = 1 AND t.a_la_une = 1'],
                ['Affichés', 't.publie = 1'],
                ['Masqués', 't.publie = 0'],
            ],
            // Profil tiré du compte de la personne : genre du membre individuel, type de l'organisation
            'depuis' => 'FROM temoignages t LEFT JOIN profils_individuels pi ON pi.utilisateur_id = t.utilisateur_id LEFT JOIN profils_organisations po ON po.utilisateur_id = t.utilisateur_id',
            'champs_select' => "t.*, " . SQL_PROFIL_TEMOIGNAGE . " AS profil",
            'filtres' => [['profil', 'Tous les profils', PROFILS_TEMOIGNAGE, SQL_PROFIL_TEMOIGNAGE]],
            'peut_ajouter' => false, 'vue_fiche' => 'admin/fiche_temoignage',
            'actions' => [
                'afficher' => ['Afficher sur le site', fn ($ids) => requete('UPDATE temoignages SET publie = 1 WHERE id IN (' . marqueurs($ids) . ')', $ids)->rowCount() . ' témoignage(s) affiché(s).'],
                'retirer' => ['Retirer du site', fn ($ids) => requete('UPDATE temoignages SET publie = 0, a_la_une = 0 WHERE id IN (' . marqueurs($ids) . ')', $ids)->rowCount() . ' témoignage(s) retiré(s).'],
                'une' => ["Mettre en avant", fn ($ids) => requete('UPDATE temoignages SET a_la_une = 1, publie = 1 WHERE id IN (' . marqueurs($ids) . ')', $ids)->rowCount() . " témoignage(s) mis en avant : ils passent en premier sur l'accueil et sur la page Témoignages."],
                'pas_une' => ["Ne plus mettre en avant", fn ($ids) => requete('UPDATE temoignages SET a_la_une = 0 WHERE id IN (' . marqueurs($ids) . ')', $ids)->rowCount() . " témoignage(s) ne sont plus mis en avant."],
            ],
            'nom_element' => fn ($l) => $l['nom'],
            'groupes' => [
                [null, 'champs' => [
                    // Témoignage envoyé par un membre : ses mots restent les siens (lecture seule)
                    'texte' => ['type' => 'zone', 'libelle' => 'Témoignage', 'max' => 800, 'lignes' => 5, 'lecture_seule' => fn ($l) => !empty($l['utilisateur_id']), 'aide' => "Un témoignage envoyé par un membre ne se modifie pas : vous pouvez l'afficher, le retirer ou le mettre sur l'accueil."],
                    'nom' => ['type' => 'texte', 'libelle' => 'Nom', 'max' => 120, 'largeur' => 'demi', 'lecture_seule' => fn ($l) => !empty($l['utilisateur_id'])],
                    'role' => ['type' => 'texte', 'libelle' => 'Rôle ou parcours', 'requis' => false, 'max' => 150, 'largeur' => 'demi', 'lecture_seule' => fn ($l) => !empty($l['utilisateur_id'])],
                    'categorie' => ['type' => 'select', 'libelle' => 'Profil', 'requis' => false, 'options' => ['' => 'Non précisé'] + PROFILS_TEMOIGNAGE, 'aide' => 'Sert au tri des témoignages.'],
                ]],
                ['Photo', 'champs' => ['photo' => ['type' => 'image', 'libelle' => 'Photo', 'requis' => false, 'dossier' => 'temoignages', 'largeur_max' => 400]]],
                ['Affichage', 'cote' => true, 'description' => "À publier uniquement avec l'accord de la personne. Les témoignages envoyés par les membres depuis leur espace arrivent ici, masqués.", 'champs' => [
                    'publie' => ['type' => 'case', 'libelle' => 'Afficher sur le site (page Témoignages)'],
                    'a_la_une' => ['type' => 'case', 'libelle' => "Mettre sur l'accueil", 'aide' => "L'accueil montre trois témoignages : ceux cochés ici, les plus récents d'abord."],
                    'origine' => ['type' => 'lecture', 'libelle' => 'Origine', 'html' => fn ($l) => empty($l['utilisateur_id']) ? "Ajouté dans l'administration" : 'Envoyé par le membre depuis son espace' . (!empty($l['envoye_le']) ? ', le ' . e(date_longue($l['envoye_le'])) : '') . ' · <a class="ycm-lien" href="' . url('/gestion/comptes/' . (int) $l['utilisateur_id']) . '">Voir le compte</a>'],
                    'ordre' => ['type' => 'nombre', 'libelle' => "Ordre d'affichage", 'defaut' => 0],
                ]],
            ],
        ],

        // ------------------------------------------------------------------
        'partenaires' => [
            'table' => 'partenaires', 'titre' => 'Partenaires',
            'description' => "Les partenaires affichés sur le site. L'ordre ici est celui de l'accueil.",
            'libelle_ajout' => 'Ajouter un partenaire', 'vue_liste' => 'admin/liste_partenaires',
            'ordre' => "t.categorie = 'prive', t.ordre, t.nom", 'par_page' => 60, 'recherche' => ['t.nom'],
            'onglets' => [
                ['Institutionnels et techniques', "t.categorie = 'institutionnel'"],
                ['Secteur privé', "t.categorie = 'prive'"],
            ],
            'groupes' => [
                [null, 'champs' => [
                    'nom' => ['type' => 'texte', 'libelle' => 'Nom', 'max' => 150],
                    'categorie' => ['type' => 'select', 'libelle' => 'Famille', 'options' => CATEGORIES_PARTENAIRE, 'defaut' => 'institutionnel', 'aide' => "Ministères, agences des Nations unies, coopérations : institutionnel. Entreprises, banques, opérateurs : secteur privé."],
                    'lien' => ['type' => 'url', 'libelle' => 'Site web', 'requis' => false],
                ]],
                ['Logo', 'champs' => ['logo' => ['type' => 'image', 'libelle' => 'Logo', 'requis' => false, 'dossier' => 'partenaires', 'largeur_max' => 600]]],
                ['Affichage', 'cote' => true, 'champs' => [
                    'actif' => ['type' => 'case', 'libelle' => 'Afficher sur le site', 'defaut' => 1],
                    'ordre' => ['type' => 'nombre', 'libelle' => "Ordre d'affichage", 'defaut' => 0],
                ]],
            ],
        ],

        // ------------------------------------------------------------------
        'membres' => [
            'table' => 'profils_individuels', 'titre' => 'Membres individuels',
            'description' => "Jeunes, étudiants, jeunes professionnels, entrepreneurs, porteurs de projets, innovateurs et leaders d'associations inscrits.",
            'peut_ajouter' => false, 'peut_supprimer' => false,
            'champs_select' => "t.*, u.email, u.actif, u.inscrit_le, u.email_confirme,
                (SELECT COUNT(*) FROM demandes_adhesion d WHERE d.utilisateur_id = t.utilisateur_id AND d.statut = 'acceptee') AS nb_communautes",
            'depuis' => 'FROM profils_individuels t JOIN utilisateurs u ON u.id = t.utilisateur_id',
            'ordre' => 'u.inscrit_le DESC',
            'onglets' => [['Actifs', 'u.actif = 1'], ['Inactifs', 'u.actif = 0']],
            'filtres' => [
                ['genre', 'Femmes et hommes', GENRES, 't.genre'],
                ['age', "Tous les âges", array_map(fn ($tr) => $tr[0], TRANCHES_AGE), "(CASE " . implode(' ', array_map(fn ($cle, $tr) => "WHEN TIMESTAMPDIFF(YEAR, t.date_naissance, CURDATE()) BETWEEN {$tr[1]} AND {$tr[2]} THEN '$cle'", array_keys(TRANCHES_AGE), TRANCHES_AGE)) . " END)"],
                ['region', 'Toutes les régions', REGIONS, 't.region'],
                ['categorie', 'Toutes catégories', CATEGORIES_INDIVIDUELLES, 't.categorie'],
            ],
            'recherche' => ['t.prenom', 't.nom', 'u.email', 't.domaine', 't.ville', 't.region', 't.telephone', 't.presentation'],
            'nom_element' => fn ($l) => $l['prenom'] . ' ' . $l['nom'],
            'colonnes' => [
                ['Membre', fn ($l) => nom_et_detail($l['prenom'] . ' ' . $l['nom'], $l['email'], avatar(mb_strtoupper(mb_substr($l['prenom'], 0, 1) . mb_substr($l['nom'], 0, 1)), (int) $l['id'], $l['photo'] ?: null)), 'lien' => true],
                ['Catégorie', fn ($l) => e(CATEGORIES_INDIVIDUELLES[$l['categorie']] ?? '')],
                ['Genre et âge', fn ($l) => e(implode(', ', array_filter([GENRES[$l['genre'] ?? ''] ?? '', ($a = age($l['date_naissance'] ?? null)) !== null ? $a . ' ans' : '']))) ?: '<span class="ycm-discret">Non renseigné</span>'],
                ['Région', fn ($l) => e(REGIONS[$l['region']] ?? '—')],
                ['Communautés', fn ($l) => $l['nb_communautes'] . ' communauté' . pluriel((int) $l['nb_communautes'])],
                ['Inscription', fn ($l) => e(date_chiffres($l['inscrit_le']))],
                ['Statut', fn ($l) => $l['actif'] ? badge('Actif', 'vert') : badge('Inactif', 'gris')],
            ],
            'url_voir' => fn ($l) => ($l['visible'] && $l['actif']) ? url('/compte/membres/' . $l['utilisateur_id']) : null,
            'export' => ['Prénom' => 'prenom', 'Nom' => 'nom', 'E-mail' => 'email', 'Téléphone' => 'telephone', 'Catégorie' => fn ($l) => CATEGORIES_INDIVIDUELLES[$l['categorie']] ?? '',
                'Genre' => fn ($l) => GENRES[$l['genre'] ?? ''] ?? '', 'Âge' => fn ($l) => age($l['date_naissance'] ?? null) ?? '',
                'Région' => fn ($l) => REGIONS[$l['region']] ?? '', 'Ville' => 'ville', 'Domaine' => 'domaine', 'Communautés' => 'nb_communautes', 'Inscription' => fn ($l) => date_chiffres($l['inscrit_le'])],
            'groupes' => [
                ['Identité', 'champs' => [
                    'prenom' => ['type' => 'texte', 'libelle' => 'Prénom', 'max' => 80, 'largeur' => 'demi'],
                    'nom' => ['type' => 'texte', 'libelle' => 'Nom', 'max' => 80, 'largeur' => 'demi'],
                    'genre' => ['type' => 'select', 'libelle' => 'Genre', 'options' => ['' => 'Non renseigné'] + GENRES, 'requis' => false, 'largeur' => 'demi'],
                    'date_naissance' => ['type' => 'date', 'libelle' => 'Date de naissance', 'requis' => false, 'largeur' => 'demi', 'aide' => 'Privée.'],
                    'categorie' => ['type' => 'select', 'libelle' => 'Profil', 'options' => CATEGORIES_INDIVIDUELLES, 'largeur' => 'demi'],
                    'domaine' => ['type' => 'texte', 'libelle' => "Domaine d'activité ou d'études", 'requis' => false, 'max' => 150, 'largeur' => 'demi'],
                    'photo' => ['type' => 'image', 'libelle' => 'Photo', 'requis' => false, 'dossier' => 'profils', 'largeur_max' => 800],
                ]],
                ['Localisation et contact', 'champs' => [
                    'region' => ['type' => 'select', 'libelle' => 'Région', 'options' => REGIONS, 'largeur' => 'demi'],
                    'ville' => ['type' => 'texte', 'libelle' => 'Ville', 'requis' => false, 'max' => 100, 'largeur' => 'demi'],
                    'telephone' => ['type' => 'texte', 'libelle' => 'Téléphone', 'requis' => false, 'max' => 30, 'aide' => 'Privé : jamais affiché sur le site.'],
                ]],
                ['Présentation', 'champs' => [
                    'presentation' => ['type' => 'zone', 'libelle' => 'Présentation', 'requis' => false, 'max' => 1500, 'lignes' => 4],
                    'liens' => ['type' => 'lecture', 'libelle' => 'Liens publics', 'html' => fn ($l) => implode('<br>', array_map(fn ($k) => e($k['nom']) . ' : <a class="ycm-lien" href="' . e($k['url']) . '" target="_blank" rel="noopener">' . e($k['url']) . '</a>', liens_profil((int) $l['utilisateur_id']))) ?: 'Aucun (le membre les ajoute depuis son espace)'],
                ]],
                ['Compte', 'cote' => true, 'champs' => [
                    'lien_compte' => ['type' => 'lecture', 'libelle' => 'Compte de connexion', 'html' => fn ($l) => '<a class="ycm-lien" href="' . url('/gestion/comptes/' . $l['utilisateur_id']) . '">' . e($l['email']) . '</a>'],
                    'visible' => ['type' => 'case', 'libelle' => 'Visible dans la communauté'],
                ]],
            ],
            'avant' => function (array &$d, ?array $ancien) { $d['modifie_le'] = maintenant(); },
        ],

        // ------------------------------------------------------------------
        'organisations' => [
            'table' => 'profils_organisations', 'titre' => 'Organisations et entreprises',
            'description' => 'Entreprises, startups, associations et ONG inscrites.',
            'peut_ajouter' => false, 'peut_supprimer' => false, 'libelle_tous' => 'Toutes',
            'champs_select' => "t.*, u.email, u.actif, u.inscrit_le,
                (SELECT COUNT(*) FROM demandes_adhesion d WHERE d.utilisateur_id = t.utilisateur_id AND d.statut = 'acceptee') AS nb_communautes",
            'depuis' => 'FROM profils_organisations t JOIN utilisateurs u ON u.id = t.utilisateur_id',
            'ordre' => 'u.inscrit_le DESC',
            'onglets' => [['Entreprises', "t.type_structure = 'entreprise'"], ['Startups', "t.type_structure = 'startup'"], ['Associations', "t.type_structure = 'association'"], ['ONG', "t.type_structure = 'ong'"]],
            'filtres' => [['region', 'Toutes les régions', REGIONS, 't.region']],
            'recherche' => ['t.nom_structure', 'u.email', 't.secteur', 't.personne_contact', 't.ville', 't.region', 't.presentation'],
            'nom_element' => fn ($l) => $l['nom_structure'],
            'colonnes' => [
                ['Organisation', fn ($l) => nom_et_detail($l['nom_structure'], TYPES_STRUCTURE[$l['type_structure']] ?? '', avatar(initiales_organisation($l['nom_structure']), (int) $l['id'], $l['logo'] ?: null)), 'lien' => true],
                ['Région', fn ($l) => e(REGIONS[$l['region']] ?? '—')],
                ['Contact', fn ($l) => e($l['personne_contact'] ?: '—')],
                ['Communautés', fn ($l) => $l['nb_communautes'] . ' communauté' . pluriel((int) $l['nb_communautes'])],
                ['Statut', fn ($l) => $l['actif'] ? badge('Active', 'vert') : badge('Inactive', 'gris')],
            ],
            'url_voir' => fn ($l) => ($l['visible'] && $l['actif']) ? url('/compte/membres/' . $l['utilisateur_id']) : null,
            'export' => ['Structure' => 'nom_structure', 'Type' => fn ($l) => TYPES_STRUCTURE[$l['type_structure']] ?? '', 'Secteur' => 'secteur', 'E-mail' => 'email',
                'Personne de contact' => 'personne_contact', 'Région' => fn ($l) => REGIONS[$l['region']] ?? '', 'Ville' => 'ville', 'Inscription' => fn ($l) => date_chiffres($l['inscrit_le'])],
            'groupes' => [
                ['Structure', 'champs' => [
                    'nom_structure' => ['type' => 'texte', 'libelle' => 'Nom de la structure', 'max' => 150],
                    'type_structure' => ['type' => 'select', 'libelle' => 'Type de structure', 'options' => TYPES_STRUCTURE, 'largeur' => 'demi'],
                    'secteur' => ['type' => 'texte', 'libelle' => "Secteur d'activité", 'requis' => false, 'max' => 150, 'largeur' => 'demi'],
                    'logo' => ['type' => 'image', 'libelle' => 'Logo', 'requis' => false, 'dossier' => 'organisations', 'largeur_max' => 800],
                ]],
                ['Contact', 'champs' => [
                    'personne_contact' => ['type' => 'texte', 'libelle' => 'Personne de contact', 'requis' => false, 'max' => 120, 'largeur' => 'demi', 'aide' => 'Privé : jamais affiché sur le site.'],
                    'email_contact_public' => ['type' => 'email', 'libelle' => 'E-mail de contact public', 'requis' => false, 'largeur' => 'demi'],
                    'region' => ['type' => 'select', 'libelle' => 'Région', 'options' => REGIONS, 'largeur' => 'demi'],
                    'ville' => ['type' => 'texte', 'libelle' => 'Ville', 'requis' => false, 'max' => 100, 'largeur' => 'demi'],
                ]],
                ['Présentation', 'champs' => [
                    'presentation' => ['type' => 'zone', 'libelle' => 'Présentation', 'requis' => false, 'max' => 1500, 'lignes' => 4],
                    'liens' => ['type' => 'lecture', 'libelle' => 'Liens publics', 'html' => fn ($l) => implode('<br>', array_map(fn ($k) => e($k['nom']) . ' : <a class="ycm-lien" href="' . e($k['url']) . '" target="_blank" rel="noopener">' . e($k['url']) . '</a>', liens_profil((int) $l['utilisateur_id']))) ?: 'Aucun (le membre les ajoute depuis son espace)'],
                ]],
                ['Compte', 'cote' => true, 'champs' => [
                    'lien_compte' => ['type' => 'lecture', 'libelle' => 'Compte de connexion', 'html' => fn ($l) => '<a class="ycm-lien" href="' . url('/gestion/comptes/' . $l['utilisateur_id']) . '">' . e($l['email']) . '</a>'],
                    'visible' => ['type' => 'case', 'libelle' => 'Visible dans la communauté'],
                ]],
            ],
            'avant' => function (array &$d, ?array $ancien) { $d['modifie_le'] = maintenant(); },
        ],

        // ------------------------------------------------------------------
        'communautes' => [
            'table' => 'communautes', 'titre' => 'Communautés',
            'description' => "Les communautés thématiques et les demandes d'intégration envoyées par les membres.",
            'libelle_ajout' => 'Créer une communauté', 'vue_liste' => 'admin/liste_communautes',
            'champs_select' => "t.*,
                (SELECT COUNT(*) FROM demandes_adhesion d WHERE d.communaute_id = t.id AND d.statut = 'acceptee') AS nb_membres,
                (SELECT COUNT(*) FROM demandes_adhesion d WHERE d.communaute_id = t.id AND d.statut = 'en_attente') AS nb_attente,
                (SELECT COUNT(*) FROM opportunite_communaute oc WHERE oc.communaute_id = t.id) AS nb_opportunites",
            'ordre' => 't.ordre, t.nom', 'par_page' => 60, 'recherche' => ['t.nom', 't.public_vise', 't.description'],
            'peut_supprimer' => false,
            'aide_cote' => "Une communauté désactivée n'apparaît plus sur le site, sans perte de données. Pour la retirer, décochez « Active ».",
            'url_voir' => fn ($l) => $l['active'] ? url('/communaute/' . $l['slug']) : null,
            'groupes' => [
                [null, 'champs' => [
                    'nom' => ['type' => 'texte', 'libelle' => 'Nom', 'max' => 120],
                    'public_vise' => ['type' => 'texte', 'libelle' => 'Public visé', 'max' => 255, 'aide' => 'Affiché sur les cartes, sous le nom.'],
                    'description' => ['type' => 'zone', 'libelle' => 'Description', 'requis' => false, 'lignes' => 4],
                    'activites' => ['type' => 'zone', 'libelle' => "Ce qu'on y fait", 'requis' => false, 'aide' => 'Une activité par ligne.', 'lignes' => 4],
                ]],
                ['Image', 'champs' => ['image' => ['type' => 'image', 'libelle' => 'Image', 'requis' => false, 'dossier' => 'communautes']]],
                ['Affichage', 'cote' => true, 'champs' => [
                    'active' => ['type' => 'case', 'libelle' => 'Active', 'defaut' => 1],
                    'ordre' => ['type' => 'nombre', 'libelle' => "Ordre d'affichage", 'defaut' => 0],
                    'slug' => ['type' => 'slug', 'libelle' => 'Adresse (URL)', 'requis' => false, 'max' => 140, 'aide' => 'Laisser vide : elle est créée à partir du nom.'],
                ]],
            ],
            'avant' => function (array &$d, ?array $ancien) {
                $d['slug'] = slug_unique('communautes', ($d['slug'] ?? '') ?: $d['nom'], $ancien['id'] ?? null);
                if ((int) valeur('SELECT COUNT(*) FROM communautes WHERE nom = ? AND id <> ?', [$d['nom'], $ancien['id'] ?? 0])) {
                    throw new InvalidArgumentException('nom|Une communauté porte déjà ce nom.');
                }
            },
        ],

        // ------------------------------------------------------------------
        'demandes' => [
            'table' => 'demandes_adhesion', 'titre' => "Demandes d'intégration",
            'description' => 'Toutes les demandes envoyées par les membres pour rejoindre une communauté.',
            'peut_ajouter' => false, 'libelle_tous' => 'Toutes',
            'champs_select' => 't.*, u.email, c.nom AS communaute_nom,
                COALESCE(CONCAT(pi.prenom, " ", pi.nom), po.nom_structure, u.email) AS membre_nom',
            'depuis' => 'FROM demandes_adhesion t JOIN utilisateurs u ON u.id = t.utilisateur_id JOIN communautes c ON c.id = t.communaute_id
                LEFT JOIN profils_individuels pi ON pi.utilisateur_id = u.id LEFT JOIN profils_organisations po ON po.utilisateur_id = u.id',
            'ordre' => "t.statut = 'en_attente' DESC, t.demandee_le DESC",
            'libelle_ouvrir' => fn ($l) => ($l['statut'] ?? '') === 'en_attente' ? 'Traiter' : 'Voir',
            'onglets' => [['En attente', "t.statut = 'en_attente'"], ['Acceptées', "t.statut = 'acceptee'"], ['Refusées', "t.statut = 'refusee'"]],
            'filtres' => [['communaute', 'Toutes les communautés', 'options_communautes', 't.communaute_id']],
            'recherche' => ['u.email', 'c.nom', 'pi.nom', 'pi.prenom', 'po.nom_structure'],
            'nom_element' => fn ($l) => $l['membre_nom'] . ' → ' . $l['communaute_nom'],
            'colonnes' => [
                ['Membre', fn ($l) => nom_et_detail($l['membre_nom'], $l['email'], avatar(initiales($l['membre_nom']), (int) $l['id']))],
                ['Communauté', fn ($l) => '<strong style="color:var(--ycm-vert)">' . e($l['communaute_nom']) . '</strong>'],
                ['Date', fn ($l) => e(date_chiffres($l['demandee_le']))],
                ['Statut', fn ($l) => badge(STATUTS_DEMANDE[$l['statut']] ?? '', ['en_attente' => 'orange', 'acceptee' => 'vert', 'refusee' => 'rouge'][$l['statut']] ?? 'gris')],
                ['', fn ($l) => liens_ligne(['Voir la demande' => url('/gestion/demandes/' . $l['id'])]), 'classe' => 'ycm-droite'],
            ],
            'pas_de_lien_modifier' => true,
            'actions' => [
                'accepter' => ['Accepter les demandes sélectionnées', fn ($ids) => traiter_demandes($ids, 'acceptee') . ' demande(s) acceptée(s).'],
                'refuser' => ['Refuser les demandes sélectionnées', fn ($ids) => traiter_demandes($ids, 'refusee') . ' demande(s) refusée(s).'],
            ],
            'export' => ['Membre' => 'membre_nom', 'E-mail' => 'email', 'Communauté' => 'communaute_nom', 'Date' => fn ($l) => date_chiffres($l['demandee_le']), 'Statut' => fn ($l) => STATUTS_DEMANDE[$l['statut']] ?? ''],
        ],

        // ------------------------------------------------------------------
        'messages' => [
            'table' => 'messages_contact', 'titre' => 'Messages de contact',
            'description' => 'Les messages envoyés depuis le formulaire de contact.',
            'peut_ajouter' => false, 'vue_liste' => 'admin/liste_messages', 'par_page' => 50,
            'ordre' => 't.traite, t.recu_le DESC',
            'libelle_ouvrir' => fn ($l) => empty($l['traite']) ? 'Lire et traiter' : 'Lire',
            'onglets' => [['Non lus', 't.traite = 0'], ['Traités', 't.traite = 1']],
            'filtres' => [['profil', 'Tous les profils', PROFILS_CONTACT, 't.profil']],
            'recherche' => ['t.nom', 't.email', 't.objet', 't.message'],
            'nom_element' => fn ($l) => $l['objet'],
            'export' => ['Reçu le' => fn ($l) => date_chiffres($l['recu_le']), 'Nom' => 'nom', 'E-mail' => 'email', 'Profil' => fn ($l) => PROFILS_CONTACT[$l['profil']] ?? '', 'Objet' => 'objet', 'Message' => 'message', 'Traité' => fn ($l) => $l['traite'] ? 'oui' : 'non'],
            'actions' => [
                'traites' => ['Marquer comme traités', fn ($ids) => requete('UPDATE messages_contact SET traite = 1 WHERE id IN (' . marqueurs($ids) . ')', $ids)->rowCount() . ' message(s) marqué(s) comme traité(s).'],
                'a_traiter' => ['Remettre à traiter', fn ($ids) => requete('UPDATE messages_contact SET traite = 0 WHERE id IN (' . marqueurs($ids) . ')', $ids)->rowCount() . ' message(s) remis à traiter.'],
                'supprimer' => ['Supprimer les messages sélectionnés', fn ($ids) => requete('DELETE FROM messages_contact WHERE id IN (' . marqueurs($ids) . ')', $ids)->rowCount() . ' message(s) supprimé(s).'],
            ],
        ],

        // ------------------------------------------------------------------
        'newsletter' => [
            'table' => 'abonnes_newsletter', 'titre' => 'Abonnés newsletter',
            'description' => 'Les personnes qui reçoivent les opportunités et actualités par e-mail.',
            'libelle_ajout' => 'Ajouter un abonné', 'par_page' => 50,
            'ordre' => 't.inscrit_le DESC',
            'onglets' => [['Abonnés', 't.actif = 1'], ['Désabonnés', 't.actif = 0']],
            'recherche' => ['t.email'],
            'nom_element' => fn ($l) => $l['email'],
            'colonnes' => [
                ['E-mail', fn ($l) => '<strong>' . e($l['email']) . '</strong>', 'lien' => true],
                ['Inscription', fn ($l) => e(date_chiffres($l['inscrit_le']))],
                ['Statut', fn ($l) => $l['actif'] ? badge('Abonné', 'vert') : badge('Désabonné', 'gris')],
                ['Action', fn ($l) => '<button type="submit" form="basculer-' . (int) $l['id'] . '" class="ycm-bouton-lien">' . ($l['actif'] ? 'Désabonner' : 'Réabonner') . '</button>'],
            ],
            'pas_de_lien_modifier' => true,
            'actions' => [
                'desabonner' => ['Désabonner', fn ($ids) => requete('UPDATE abonnes_newsletter SET actif = 0 WHERE id IN (' . marqueurs($ids) . ')', $ids)->rowCount() . ' abonné(s) désactivé(s).'],
                'reabonner' => ['Réabonner', fn ($ids) => requete('UPDATE abonnes_newsletter SET actif = 1 WHERE id IN (' . marqueurs($ids) . ')', $ids)->rowCount() . ' abonné(s) réactivé(s).'],
            ],
            'export' => ['E-mail' => 'email', 'Inscription' => fn ($l) => date_chiffres($l['inscrit_le']), 'Actif' => fn ($l) => $l['actif'] ? 'oui' : 'non'],
            'groupes' => [
                [null, 'champs' => [
                    'email' => ['type' => 'email', 'libelle' => 'Adresse e-mail'],
                    'actif' => ['type' => 'case', 'libelle' => 'Abonné', 'defaut' => 1],
                ]],
            ],
            'avant' => function (array &$d, ?array $ancien) {
                $d['email'] = mb_strtolower($d['email']);
                if ((int) valeur('SELECT COUNT(*) FROM abonnes_newsletter WHERE email = ? AND id <> ?', [$d['email'], $ancien['id'] ?? 0])) {
                    throw new InvalidArgumentException('email|Cette adresse est déjà dans la liste.');
                }
                if (!$ancien) {
                    $d['inscrit_le'] = maintenant();
                }
            },
        ],

        // ------------------------------------------------------------------
        'chiffres' => [
            'table' => 'chiffres_cles', 'titre' => 'Chiffres clés',
            'description' => "Les chiffres affichés sur l'accueil, dans la section du chapitre. Seuls les trois premiers publiés apparaissent.",
            'libelle_ajout' => 'Ajouter un chiffre', 'vue_liste' => 'admin/liste_chiffres', 'ordre' => 't.ordre, t.id',
            'nom_element' => fn ($l) => $l['valeur'] . ' ' . $l['libelle'],
            'groupes' => [
                [null, 'champs' => [
                    'valeur' => ['type' => 'texte', 'libelle' => 'Valeur', 'max' => 30, 'aide' => 'Exemple : 1 200 ou 15+'],
                    'libelle' => ['type' => 'texte', 'libelle' => 'Libellé', 'max' => 120],
                ]],
                ['Affichage', 'cote' => true, 'champs' => [
                    'publie' => ['type' => 'case', 'libelle' => 'Publié', 'aide' => "Ne publier qu'un chiffre vérifié."],
                    'ordre' => ['type' => 'nombre', 'libelle' => "Ordre d'affichage", 'defaut' => 0],
                ]],
            ],
        ],

        // ------------------------------------------------------------------
        'ministre' => [
            'table' => 'mot_ministre', 'titre' => 'Mot du ministre', 'fiche_unique' => true,
            'description' => "Le message affiché avant la section Communauté sur l'accueil, et en entier sur la page À propos.",
            'groupes' => [
                [null, 'description' => "Tant que « Publié » n'est pas coché, l'accueil montre le texte proposé par la maquette, avec la mention « Proposition de texte, à valider par le cabinet du ministre ».", 'champs' => [
                    'nom' => ['type' => 'texte', 'libelle' => 'Nom du ministre', 'requis' => false, 'max' => 150],
                    'titre_officiel' => ['type' => 'texte', 'libelle' => 'Titre officiel', 'requis' => false, 'max' => 255],
                    'message' => ['type' => 'zone', 'libelle' => 'Message complet', 'requis' => false, 'aide' => "Affiché en entier sur l'accueil. " . AIDE_MISE_EN_FORME, 'lignes' => 10],
                    'extrait' => ['type' => 'zone', 'libelle' => 'Extrait', 'requis' => false, 'max' => 600, 'aide' => 'Utilisé seulement si le message complet est vide.', 'lignes' => 3],
                ]],
                ['Photo officielle', 'cote' => true, 'champs' => [
                    'photo' => ['type' => 'image', 'libelle' => 'Photo officielle', 'requis' => false, 'dossier' => 'ministre', 'largeur_max' => 1000, 'aide' => 'Format portrait.'],
                    'publie' => ['type' => 'case', 'libelle' => 'Publié', 'aide' => "Ne cocher qu'après validation officielle du texte et de la photo."],
                ]],
            ],
        ],

        // ------------------------------------------------------------------
        'types-opportunites' => [
            'table' => 'types_opportunite', 'titre' => "Types d'opportunités", 'rubrique' => 'opportunites',
            'description' => "Les catégories proposées sur la page Opportunités (filtre, couleur des cartes). Un type utilisé par des opportunités ne peut pas être supprimé.",
            'libelle_ajout' => 'Ajouter un type', 'ordre' => 't.ordre, t.nom',
            'champs_select' => 't.*, (SELECT COUNT(*) FROM opportunites o WHERE o.type = t.slug) AS nb',
            'colonnes' => [
                ['Type', fn ($l) => '<strong>' . e($l['nom']) . '</strong>', 'lien' => true],
                ['Couleur', fn ($l) => point_couleur(COULEURS_CHARTE[$l['couleur']] ?? $l['couleur'], $l['couleur'])],
                ['Opportunités', fn ($l) => (int) $l['nb']],
                ['Ordre', fn ($l) => (int) $l['ordre']],
            ],
            'groupes' => [
                [null, 'champs' => [
                    'nom' => ['type' => 'texte', 'libelle' => 'Nom du type', 'max' => 80, 'aide' => 'Exemple : Concours, Volontariat, Financement…'],
                ]],
                ['Affichage', 'cote' => true, 'champs' => [
                    'couleur' => ['type' => 'select', 'libelle' => 'Couleur', 'options' => COULEURS_CHARTE],
                    'ordre' => ['type' => 'nombre', 'libelle' => "Ordre d'affichage", 'defaut' => 0],
                ]],
            ],
            'avant' => function (array &$d, ?array $ancien) {
                // L'adresse du type ne change plus une fois créée : les opportunités y sont rattachées
                $d['slug'] = $ancien['slug'] ?? slug_unique('types_opportunite', $d['nom'], null);
            },
            'avant_suppression' => function (array $ligne) {
                $n = (int) valeur('SELECT COUNT(*) FROM opportunites WHERE type = ?', [$ligne['slug']]);
                return $n ? "Ce type est utilisé par $n opportunité" . pluriel($n) . " : changez d'abord leur type, puis supprimez-le." : null;
            },
        ],

        'categories-actualites' => [
            'table' => 'categories_actualite', 'titre' => "Catégories d'actualités", 'rubrique' => 'actualites',
            'description' => "Les catégories proposées dans le filtre de la page Actualités. Une catégorie utilisée par des actualités ne peut pas être supprimée.",
            'libelle_ajout' => 'Ajouter une catégorie', 'ordre' => 't.ordre, t.nom',
            'champs_select' => 't.*, (SELECT COUNT(*) FROM actualites a WHERE a.categorie = t.slug) AS nb',
            'colonnes' => [
                ['Catégorie', fn ($l) => '<strong>' . e($l['nom']) . '</strong>', 'lien' => true],
                ['Couleur', fn ($l) => point_couleur(COULEURS_CHARTE[$l['couleur']] ?? $l['couleur'], $l['couleur'])],
                ['Actualités', fn ($l) => (int) $l['nb']],
                ['Ordre', fn ($l) => (int) $l['ordre']],
            ],
            'groupes' => [
                [null, 'champs' => [
                    'nom' => ['type' => 'texte', 'libelle' => 'Nom de la catégorie', 'max' => 80, 'aide' => 'Exemple : Événement, Atelier, Communiqué…'],
                ]],
                ['Affichage', 'cote' => true, 'champs' => [
                    'couleur' => ['type' => 'select', 'libelle' => 'Couleur', 'options' => COULEURS_CHARTE],
                    'ordre' => ['type' => 'nombre', 'libelle' => "Ordre d'affichage", 'defaut' => 0],
                ]],
            ],
            'avant' => function (array &$d, ?array $ancien) {
                $d['slug'] = $ancien['slug'] ?? slug_unique('categories_actualite', $d['nom'], null);
            },
            'avant_suppression' => function (array $ligne) {
                $n = (int) valeur('SELECT COUNT(*) FROM actualites WHERE categorie = ?', [$ligne['slug']]);
                return $n ? "Cette catégorie est utilisée par $n actualité" . pluriel($n) . " : changez d'abord leur catégorie, puis supprimez-la." : null;
            },
        ],

        'axes' => [
            'table' => 'axes', 'titre' => "Axes d'action",
            'description' => "Les façons d'agir du chapitre, affichées sur l'accueil et la page À propos.",
            'libelle_ajout' => 'Ajouter un axe', 'ordre' => 't.ordre, t.nom',
            'colonnes' => [
                ['Nom', fn ($l) => '<strong>' . e($l['nom']) . '</strong>', 'lien' => true],
                ['Couleur', fn ($l) => point_couleur(COULEURS_CHARTE[$l['couleur']] ?? '', $l['couleur'])],
                ['Ordre', fn ($l) => (int) $l['ordre']],
            ],
            'groupes' => [
                [null, 'champs' => [
                    'nom' => ['type' => 'texte', 'libelle' => 'Nom', 'max' => 120],
                    'description' => ['type' => 'zone', 'libelle' => 'Description', 'lignes' => 3],
                    'elements' => ['type' => 'zone', 'libelle' => "Exemples d'actions", 'requis' => false, 'aide' => 'Une action par ligne (page À propos).', 'lignes' => 4],
                ]],
                ['Image', 'champs' => ['image' => ['type' => 'image', 'libelle' => 'Image', 'requis' => false, 'dossier' => 'axes', 'aide' => 'Laisser vide pour garder la photo de la maquette.']]],
                ['Affichage', 'cote' => true, 'champs' => [
                    'couleur' => ['type' => 'select', 'libelle' => 'Couleur', 'options' => COULEURS_CHARTE],
                    'ordre' => ['type' => 'nombre', 'libelle' => "Ordre d'affichage", 'defaut' => 0],
                    'slug' => ['type' => 'slug', 'libelle' => 'Adresse (URL)', 'requis' => false, 'max' => 140],
                ]],
            ],
            'avant' => function (array &$d, ?array $ancien) {
                $d['slug'] = slug_unique('axes', ($d['slug'] ?? '') ?: $d['nom'], $ancien['id'] ?? null);
            },
        ],

        // ------------------------------------------------------------------
        'parametres' => [
            'table' => 'parametres', 'titre' => 'Paramètres', 'fiche_unique' => true,
            'description' => "Coordonnées, réseaux sociaux, réglages du site et comptes d'administration.",
            'vue_formulaire_apres' => 'admin/parametres_equipe',
            'groupes' => [
                ['Coordonnées du chapitre', 'champs' => [
                    'email_contact' => ['type' => 'email', 'libelle' => 'E-mail de contact', 'requis' => false, 'largeur' => 'demi'],
                    'telephone' => ['type' => 'texte', 'libelle' => 'Téléphone', 'requis' => false, 'max' => 40, 'largeur' => 'demi'],
                    'adresse' => ['type' => 'texte', 'libelle' => 'Adresse', 'requis' => false, 'max' => 255, 'largeur' => 'demi'],
                    'horaires' => ['type' => 'texte', 'libelle' => "Jours et heures d'ouverture", 'requis' => false, 'max' => 200, 'largeur' => 'demi'],
                ]],
                ['Réseaux sociaux', 'champs' => [
                    'lien_facebook' => ['type' => 'url', 'libelle' => 'Facebook', 'requis' => false, 'largeur' => 'demi'],
                    'lien_instagram' => ['type' => 'url', 'libelle' => 'Instagram', 'requis' => false, 'largeur' => 'demi'],
                    'lien_linkedin' => ['type' => 'url', 'libelle' => 'LinkedIn', 'requis' => false, 'largeur' => 'demi'],
                    'lien_x' => ['type' => 'url', 'libelle' => 'X (Twitter)', 'requis' => false, 'largeur' => 'demi'],
                    'lien_youtube' => ['type' => 'url', 'libelle' => 'YouTube', 'requis' => false, 'largeur' => 'demi'],
                    'lien_tiktok' => ['type' => 'url', 'libelle' => 'TikTok', 'requis' => false, 'largeur' => 'demi'],
                ]],
                ["Textes de l'accueil", 'description' => 'Laisser vide pour garder les textes validés de la maquette.', 'champs' => [
                    'accroche_accueil' => ['type' => 'texte', 'libelle' => "Titre principal de l'accueil", 'requis' => false, 'max' => 200],
                    'presentation_accueil' => ['type' => 'zone', 'libelle' => "Présentation sur l'accueil", 'requis' => false, 'max' => 1200, 'lignes' => 3],
                ]],
                ['Logos du pied de page', 'description' => "Ils s'affichent en bas de toutes les pages, de part et d'autre du logo YouthConnekt Mali. De préférence des images PNG à fond transparent.", 'champs' => [
                    'logo_ministere' => ['type' => 'image', 'libelle' => "Sceau de l'État ou logo du ministère", 'requis' => false, 'dossier' => 'logos', 'largeur' => 'demi'],
                    'logo_pnud' => ['type' => 'image', 'libelle' => 'Logo du PNUD', 'requis' => false, 'dossier' => 'logos', 'largeur' => 'demi'],
                ]],
                ["Photos du premier écran de l'accueil", 'description' => "Elles défilent toutes les 5 secondes dans le grand cercle et en fond, et le titre et le texte changent avec elles. Avec la photo 1 s'affichent le titre principal et la présentation ci-dessus ; les photos 2 et 3 ont leur titre et leur texte ci-dessous. Choisissez des photos en largeur, d'au moins 1600 pixels : envoyez-en une ou prenez-la dans la photothèque. Sans photo choisie, les photos provisoires restent affichées.", 'champs' => [
                    'image_accueil_1' => ['type' => 'image', 'libelle' => 'Photo 1', 'requis' => false, 'dossier' => 'accueil', 'largeur' => 'tiers', 'phototheque' => true],
                    'image_accueil_2' => ['type' => 'image', 'libelle' => 'Photo 2', 'requis' => false, 'dossier' => 'accueil', 'largeur' => 'tiers', 'phototheque' => true],
                    'image_accueil_3' => ['type' => 'image', 'libelle' => 'Photo 3', 'requis' => false, 'dossier' => 'accueil', 'largeur' => 'tiers', 'phototheque' => true],
                    'titre_accueil_2' => ['type' => 'texte', 'libelle' => 'Titre avec la photo 2', 'requis' => false, 'max' => 200, 'largeur' => 'demi', 'aide' => 'Exemple : La jeunesse malienne se forme'],
                    'texte_accueil_2' => ['type' => 'zone', 'libelle' => 'Texte avec la photo 2', 'requis' => false, 'max' => 400, 'lignes' => 2, 'largeur' => 'demi'],
                    'titre_accueil_3' => ['type' => 'texte', 'libelle' => 'Titre avec la photo 3', 'requis' => false, 'max' => 200, 'largeur' => 'demi', 'aide' => "Exemple : La jeunesse malienne s'engage"],
                    'texte_accueil_3' => ['type' => 'zone', 'libelle' => 'Texte avec la photo 3', 'requis' => false, 'max' => 400, 'lignes' => 2, 'largeur' => 'demi'],
                ]],
                ["Petites bulles du premier écran", 'description' => "Les 4 photos rondes autour du grand cercle. Elles restent fixes : de préférence des photos de groupe ou des visages souriants, prises dans la photothèque du chapitre.", 'champs' => [
                    'bulle_accueil_1' => ['type' => 'image', 'libelle' => 'Bulle 1 (en haut à gauche)', 'requis' => false, 'dossier' => 'accueil', 'largeur' => 'demi', 'phototheque' => true],
                    'bulle_accueil_2' => ['type' => 'image', 'libelle' => 'Bulle 2 (en haut à droite)', 'requis' => false, 'dossier' => 'accueil', 'largeur' => 'demi', 'phototheque' => true],
                    'bulle_accueil_3' => ['type' => 'image', 'libelle' => 'Bulle 3 (au milieu à gauche)', 'requis' => false, 'dossier' => 'accueil', 'largeur' => 'demi', 'phototheque' => true],
                    'bulle_accueil_4' => ['type' => 'image', 'libelle' => 'Bulle 4 (au milieu à droite)', 'requis' => false, 'dossier' => 'accueil', 'largeur' => 'demi', 'phototheque' => true],
                ]],
                ['Réglages', 'cote' => true, 'champs' => [
                    'acceptation_automatique' => ['type' => 'case', 'libelle' => 'Intégration automatique aux communautés', 'aide' => "Décoché : chaque demande est validée par un administrateur (règle à confirmer par l'équipe)."],
                    'photos_provisoires' => ['type' => 'case', 'libelle' => 'Afficher « Photo provisoire »', 'aide' => 'À décocher une fois les vraies photos du chapitre en place (dossier assets/img).'],
                ]],
            ],
        ],

        // ------------------------------------------------------------------
        'comptes' => [
            'table' => 'utilisateurs', 'titre' => 'Comptes et connexions',
            'description' => "Les comptes des membres et des organisations, et à part les comptes de l'équipe d'administration.",
            'libelle_ajout' => "Créer un compte d'administration", 'ajout_discret' => true,
            'champs_select' => "t.*, pi.id AS profil_ind_id, po.id AS profil_org_id, COALESCE(CONCAT(pi.prenom, ' ', pi.nom), po.nom_structure, NULLIF(TRIM(CONCAT(t.prenom_admin, ' ', t.nom_admin)), ''), t.email) AS nom_affiche",
            'depuis' => 'FROM utilisateurs t LEFT JOIN profils_individuels pi ON pi.utilisateur_id = t.id LEFT JOIN profils_organisations po ON po.utilisateur_id = t.id',
            'ordre' => 't.inscrit_le DESC', 'par_page' => 50,
            'onglets' => [
                ['Membres individuels', "t.type_compte = 'individuel'"],
                ['Organisations', "t.type_compte = 'organisation'"],
                ["Équipe d'administration", "t.type_compte = 'administration'"],
                ['Désactivés', 't.actif = 0'],
            ],
            'recherche' => ['t.email', 'pi.prenom', 'pi.nom', 'po.nom_structure', 't.nom_admin', 't.prenom_admin'],
            'nom_element' => fn ($l) => $l['nom_affiche'],
            'colonnes' => [
                ['Compte', fn ($l) => nom_et_detail($l['nom_affiche'], $l['email'], avatar(initiales($l['nom_affiche']), (int) $l['id'])), 'lien' => true],
                ['Type', fn ($l) => ['individuel' => badge('Membre individuel', 'turquoise'), 'organisation' => badge('Organisation', 'bleu'), 'administration' => badge('Administration', 'vert')][$l['type_compte']] ?? ''],
                ['Statut', function ($l) {
                    if (!$l['actif']) return badge('Désactivé', 'rouge');
                    if ($l['type_compte'] === 'administration') return badge(ROLES_ADMIN[$l['role_admin']] ?? 'Administrateur', 'vert');
                    return badge('Actif', 'gris');
                }],
                ['Inscription', fn ($l) => e(date_chiffres($l['inscrit_le']))],
                ['Profil', fn ($l) => $l['profil_ind_id'] ? '<a class="ycm-lien" href="' . url('/gestion/membres/' . $l['profil_ind_id']) . '">Voir le profil</a>' : ($l['profil_org_id'] ? '<a class="ycm-lien" href="' . url('/gestion/organisations/' . $l['profil_org_id']) . '">Voir la fiche</a>' : '—')],
            ],
            'actions' => [
                'desactiver' => ['Désactiver les comptes sélectionnés', function ($ids) {
                    // Un administrateur ne peut pas se désactiver lui-même par erreur
                    $ids = array_values(array_filter($ids, fn ($i) => $i !== (int) utilisateur()['id']));
                    return ($ids ? requete('UPDATE utilisateurs SET actif = 0 WHERE id IN (' . marqueurs($ids) . ')', $ids)->rowCount() : 0) . ' compte(s) désactivé(s).';
                }],
                'reactiver' => ['Réactiver les comptes sélectionnés', fn ($ids) => requete('UPDATE utilisateurs SET actif = 1 WHERE id IN (' . marqueurs($ids) . ')', $ids)->rowCount() . ' compte(s) réactivé(s).'],
            ],
            'export' => ['E-mail' => 'email', 'Nom' => 'nom_affiche', 'Type' => fn ($l) => TYPES_COMPTE[$l['type_compte']] ?? '',
                'Actif' => fn ($l) => $l['actif'] ? 'oui' : 'non', 'Rôle' => fn ($l) => ROLES_ADMIN[$l['role_admin'] ?? ''] ?? '', 'Inscription' => fn ($l) => date_chiffres($l['inscrit_le'])],
            'groupes' => 'groupes_compte',
            'avant' => 'avant_compte',
        ],
    ];

    // Propositions des membres : mêmes fiches que les opportunités, dans une rubrique à part.
    // L'administrateur relit, corrige si besoin, puis publie (ou ne retient pas).
    $opp = $liste['opportunites'];
    $liste['opportunites']['condition'] = 't.propose_par IS NULL';
    $liste['opportunites']['onglets'] = [
        ['Publiées', "t.statut = 'publie'"],
        ['Brouillons', "t.statut = 'brouillon'"],
        ['Clôturées', 't.date_limite < CURDATE()'],
    ];
    $liste['opportunites']['description'] = "Les opportunités créées par l'équipe. Celles des membres sont dans « Propositions des membres ».";
    $groupes = array_values(array_filter($opp['groupes'], fn ($g) => !in_array($g[0], ['Publication', 'Origine'], true)));
    array_unshift($groupes, ['Membre', 'cote' => true, 'champs' => $opp['groupes'][array_key_last($opp['groupes'])]['champs']]);
    $groupes[] = ['Décision', 'cote' => true, 'description' => "Relisez la proposition, améliorez-la si besoin, puis validez-la ou non avec les boutons ci-dessous.", 'champs' => [
        'etat' => ['type' => 'lecture', 'libelle' => 'État actuel', 'html' => fn ($l) => ['propose' => badge('À valider', 'orange'), 'publie' => badge('Publiée sur le site', 'vert'), 'brouillon' => badge('Non retenue', 'gris')][$l['statut'] ?? 'propose'] ?? ''],
        'publie_le' => ['type' => 'datetime', 'libelle' => 'Date de publication', 'requis' => false, 'aide' => 'Vide : maintenant.', 'defaut' => 'maintenant'],
        'slug' => ['type' => 'slug', 'libelle' => 'Adresse (URL)', 'requis' => false, 'max' => 200],
    ]];
    $liste['propositions'] = array_merge($opp, [
        'titre' => 'Propositions des membres', 'rubrique' => 'opportunites', 'condition' => 't.propose_par IS NOT NULL',
        'description' => "Les opportunités envoyées par les membres depuis leur espace. Relisez-les, corrigez-les si besoin, puis publiez-les.",
        'peut_ajouter' => false, 'libelle_tous' => 'Toutes', 'ordre' => "t.statut = 'propose' DESC, t.cree_le DESC",
        // Boutons de la fiche : chacun enregistre aussi les corrections faites
        'decisions' => [
            'publie' => ['Valider et publier', 'ycm-bouton-vert', 'Valider et publier cette opportunité ?', "Vos corrections sont enregistrées et l'opportunité apparaît sur le site. Le membre en est prévenu.", 'Proposition validée et publiée sur le site.'],
            'brouillon' => ['Ne pas retenir', 'ycm-bouton-rouge-contour', 'Ne pas retenir cette proposition ?', "Elle ne sera pas publiée. Vous pourrez revenir sur cette décision plus tard.", 'Proposition non retenue.'],
        ],
        'onglets' => [
            ['À valider', "t.statut = 'propose'"],
            ['Publiées', "t.statut = 'publie'"],
            ['Non retenues', "t.statut = 'brouillon'"],
        ],
        'colonnes' => [
            ['Opportunité', fn ($l) => nom_et_detail($l['titre'], 'Proposée par ' . ($l['propose_par_nom'] ?? '') . ' le ' . date_chiffres($l['cree_le'])), 'lien' => true],
            $opp['colonnes'][1], $opp['colonnes'][2],
            ['Décision', fn ($l) => ['propose' => badge('À valider', 'orange'), 'publie' => badge('Publiée', 'vert'), 'brouillon' => badge('Non retenue', 'gris')][$l['statut']] ?? ''],
        ],
        'actions' => [
            'publier' => ['Valider et publier', fn ($ids) => requete("UPDATE opportunites SET statut = 'publie', publie_le = LEAST(publie_le, NOW()) WHERE propose_par IS NOT NULL AND id IN (" . marqueurs($ids) . ')', $ids)->rowCount() . ' proposition(s) publiée(s).'],
            'refuser' => ['Ne pas retenir', fn ($ids) => requete("UPDATE opportunites SET statut = 'brouillon' WHERE propose_par IS NOT NULL AND id IN (" . marqueurs($ids) . ')', $ids)->rowCount() . ' proposition(s) non retenue(s).'],
        ],
        'groupes' => $groupes,
    ]);
    $liste['opportunites']['menu_liste'] = [["Gérer les types d'opportunités", url('/gestion/types-opportunites')]];
    $liste['actualites']['menu_liste'] = [["Gérer les catégories d'actualités", url('/gestion/categories-actualites')]];
    $liste['types-opportunites']['parent'] = ['Opportunités', url('/gestion/opportunites')];
    $liste['categories-actualites']['parent'] = ['Actualités et événements', url('/gestion/actualites')];
    $liste['propositions']['menu_liste'] = [];

    // Candidatures aux initiatives : toutes au même endroit, triées par initiative et par rôle
    $liste['participations'] = array_merge($liste['participations'] ?? [], [
        'table' => 'participations', 'titre' => 'Participations aux initiatives', 'slug' => 'participations', 'rubrique' => 'initiatives',
        'description' => "Les membres inscrits ou candidats aux initiatives (participants, bénévoles, intervenants…).",
        'peut_ajouter' => false, 'peut_supprimer' => true, 'vue_fiche' => 'admin/fiche_participation', 'libelle_tous' => 'Toutes',
        'champs_select' => "t.*, i.titre AS initiative_titre, i.slug AS initiative_slug, u.email,
            COALESCE(CONCAT(pi.prenom, ' ', pi.nom), po.nom_structure, u.email) AS membre_nom, pi.genre, pi.date_naissance, pi.region AS region_i, pi.telephone,
            COALESCE(r.nom, t.role_nom) AS role_libelle",
        'depuis' => 'FROM participations t JOIN initiatives i ON i.id = t.initiative_id JOIN utilisateurs u ON u.id = t.utilisateur_id
            LEFT JOIN profils_individuels pi ON pi.utilisateur_id = u.id LEFT JOIN profils_organisations po ON po.utilisateur_id = u.id
            LEFT JOIN initiative_roles r ON r.id = t.role_id',
        'ordre' => "t.statut = 'en_attente' DESC, t.cree_le DESC", 'par_page' => 50,
        'recherche' => ['pi.prenom', 'pi.nom', 'po.nom_structure', 'u.email', 'i.titre'],
        'nom_element' => fn ($l) => $l['membre_nom'],
        'onglets' => [['Inscrits', "t.statut = 'inscrit'"], ['En attente', "t.statut = 'en_attente'"], ['Retenues', "t.statut = 'retenue'"], ['Non retenues', "t.statut = 'non_retenue'"], ['Présents', 't.present_le IS NOT NULL']],
        'filtres' => [
            ['initiative', 'Toutes les initiatives', fn () => array_column(lignes('SELECT DISTINCT i.id, i.titre FROM participations p JOIN initiatives i ON i.id = p.initiative_id ORDER BY i.titre'), 'titre', 'id'), 't.initiative_id'],
            ['role', 'Tous les rôles', fn () => array_column(lignes('SELECT DISTINCT r.id, CONCAT(r.nom, \' · \', i.titre) AS libelle FROM initiative_roles r JOIN initiatives i ON i.id = r.initiative_id ORDER BY i.titre, r.ordre'), 'libelle', 'id'), 't.role_id'],
        ],
        'colonnes' => [
            ['Personne', fn ($l) => nom_et_detail($l['membre_nom'], $l['email']), 'lien' => true],
            ['Rôle', fn ($l) => '<strong>' . e($l['role_libelle']) . '</strong>'],
            ['Initiative', fn ($l) => e(couper($l['initiative_titre'], 50))],
            ['Reçue le', fn ($l) => e(date_chiffres($l['cree_le']))],
            ['Décision', fn ($l) => (['inscrit' => badge('Inscrit', 'vert'), 'en_attente' => badge('En attente', 'orange'), 'retenue' => badge('Retenue', 'vert'), 'non_retenue' => badge('Non retenue', 'gris')][$l['statut']] ?? '') . ($l['present_le'] ? ' ' . badge('Présent', 'bleu') : '')],
        ],
        'actions' => [
            'retenir' => ['Retenir', fn ($ids) => requete("UPDATE participations SET statut = 'retenue', traitee_le = NOW() WHERE statut <> 'inscrit' AND id IN (" . marqueurs($ids) . ')', $ids)->rowCount() . ' participation(s) retenue(s).'],
            'ne_pas_retenir' => ['Ne pas retenir', fn ($ids) => requete("UPDATE participations SET statut = 'non_retenue', traitee_le = NOW() WHERE statut <> 'inscrit' AND id IN (" . marqueurs($ids) . ')', $ids)->rowCount() . ' participation(s) non retenue(s).'],
        ],
        'export' => [
            'Initiative' => 'initiative_titre', 'Rôle' => 'role_libelle', 'Nom' => 'membre_nom', 'E-mail' => 'email', 'Téléphone' => 'telephone',
            'Genre' => fn ($l) => GENRES[$l['genre'] ?? ''] ?? '', 'Âge' => fn ($l) => age($l['date_naissance'] ?? null) ?? '', 'Région' => fn ($l) => REGIONS[$l['region_i'] ?? ''] ?? '',
            'Décision' => fn ($l) => STATUTS_PARTICIPATION[$l['statut']] ?? '', 'Billet' => fn ($l) => billet_valable($l) ? $l['code'] : '', 'Présent' => fn ($l) => $l['present_le'] ? 'oui' : '', 'Reçue le' => fn ($l) => date_chiffres($l['cree_le']),
            'Réponses' => fn ($l) => implode(' | ', array_map(fn ($r) => $r['question'] . ' : ' . $r['reponse'], reponses_participation($l))),
        ],
        'groupes' => [[null, 'champs' => []]],
    ]);
    $liste['participations']['parent'] = ['Initiatives', url('/gestion/initiatives')];
    // Une candidature se traite (ou se consulte une fois décidée) : elle ne se « modifie » pas
    $liste['participations']['libelle_ouvrir'] = fn ($l) => ($l['statut'] ?? '') === 'en_attente' ? 'Voir et traiter' : 'Voir';
    $liste['propositions']['libelle_ouvrir'] = fn ($l) => ($l['statut'] ?? '') === 'propose' ? 'Relire et valider' : 'Voir';

    // Repère pour l'administrateur : ce qui va dans chaque rubrique (affiché en haut de la liste et du formulaire d'ajout)
    $liste['opportunites']['guide'] = [
        'Une opportunité',
        "Une porte à saisir avant une date : formation, bourse, stage, emploi, appel à candidatures, concours. Le jeune s'inscrit ou candidate, puis c'est terminé.",
    ];
    $liste['propositions']['guide'] = $liste['opportunites']['guide'];
    $liste['initiatives']['guide'] = [
        'Une initiative',
        "Un projet porté par le chapitre ou par un membre, qui dure dans le temps et auquel les jeunes peuvent prendre part : campagne, caravane, programme de mentorat, activité organisée par le chapitre. Une fois terminée, sa page devient un bilan.",
    ];
    $liste['actualites']['guide'] = [
        'Une actualité',
        "Une information : une nouvelle du chapitre, un événement annoncé, ou le compte rendu d'un événement passé (forum, sommet, rencontre).",
    ];
    $liste['communautes']['menu_liste'] = [['Demandes déjà traitées', url('/gestion/demandes')]];
    $liste['newsletter']['parent'] = ['Newsletter', url('/gestion/newsletters')];
    $liste['newsletter']['menu_liste'] = [['Nouvelle newsletter', url('/gestion/newsletters/nouvelle')]];
    return $cache = $liste;
}

/** Formulaire d'un compte : les droits d'administration ne sont modifiables que par un administrateur principal. */
function groupes_compte(?array $ligne): array
{
    $super = (utilisateur()['role_admin'] ?? '') === 'super';
    $ajout = $ligne === null;
    // Un compte membre n'a jamais de rôle : les comptes d'administration sont des comptes à part.
    $compte_admin = $ajout || $ligne['type_compte'] === 'administration';
    $acces = [
        'role_admin' => ['type' => 'select', 'libelle' => 'Rôle dans l\'administration', 'options' => ROLES_ADMIN, 'requis' => true,
            'aide' => implode(' ', array_map(fn ($k, $v) => ROLES_ADMIN[$k] . ' : ' . mb_strtolower(mb_substr($v, 0, 1)) . mb_substr($v, 1), array_keys(DESCRIPTIONS_ROLES), DESCRIPTIONS_ROLES)),
            'lecture_seule' => !$super],
        'prenom_admin' => ['type' => 'texte', 'libelle' => 'Prénom', 'requis' => false, 'max' => 80, 'largeur' => 'demi', 'aide' => "Affiché dans l'administration."],
        'nom_admin' => ['type' => 'texte', 'libelle' => 'Nom', 'requis' => false, 'max' => 80, 'largeur' => 'demi'],
    ];
    $groupes = [
        [null, 'champs' => [
            'email' => ['type' => 'email', 'libelle' => 'Adresse e-mail', 'lecture_seule' => !$ajout && !$super],
            'type_compte' => ['type' => $ajout ? 'cache' : 'lecture', 'libelle' => 'Type de compte', 'defaut' => 'administration',
                'html' => fn ($l) => e(TYPES_COMPTE[$l['type_compte']] ?? '') . ($l['type_compte'] === 'administration' ? '' : ' · <a class="ycm-lien" href="' . url('/gestion/' . ($l['type_compte'] === 'organisation' ? 'organisations' : 'membres')) . '?q=' . rawurlencode($l['email']) . '">Voir le profil</a>')],
            'nouveau_mot_de_passe' => ['type' => 'motdepasse', 'libelle' => $ajout ? 'Mot de passe' : 'Nouveau mot de passe', 'requis' => $ajout, 'virtuel' => true,
                'aide' => $ajout ? '8 caractères au minimum. Transmettez-le à la personne, qui pourra le changer depuis « Mon compte » dans l\'administration.' : 'Laisser vide pour garder le mot de passe actuel.'],
        ]],
        ...($compte_admin ? [["Accès à l'administration", 'description' => $super ? "Ce compte sert uniquement à l'administration : il n'a ni espace membre ni communautés. Réservez « Administrateur principal » aux responsables du site." : "Seul un administrateur principal peut modifier les rôles.", 'champs' => $acces]] : []),
        ['État du compte', 'cote' => true, 'champs' => [
            'actif' => ['type' => 'case', 'libelle' => 'Compte actif', 'defaut' => 1],
        ]],
    ];
    if (!$ajout) {
        $groupes[] = ['Dates', 'cote' => true, 'champs' => [
            'inscrit_le' => ['type' => 'lecture', 'libelle' => 'Inscription', 'html' => fn ($l) => e(date_longue($l['inscrit_le']))],
            'derniere_connexion' => ['type' => 'lecture', 'libelle' => 'Dernière connexion', 'html' => fn ($l) => $l['derniere_connexion'] ? e(date_longue($l['derniere_connexion']) . ', ' . date('H:i', strtotime($l['derniere_connexion']))) : 'Jamais'],
        ]];
    }
    return $groupes;
}

function avant_compte(array &$d, ?array $ancien): void
{
    $d['email'] = mb_strtolower($d['email'] ?? ($ancien['email'] ?? ''));
    if ((int) valeur('SELECT COUNT(*) FROM utilisateurs WHERE email = ? AND id <> ?', [$d['email'], $ancien['id'] ?? 0])) {
        throw new InvalidArgumentException('email|Un compte existe déjà avec cette adresse.');
    }
    $mdp = (string) ($_POST['nouveau_mot_de_passe'] ?? '');
    if ($mdp !== '') {
        if (($msg = verifier_mot_de_passe($mdp, $d['email'])) !== '') {
            throw new InvalidArgumentException('nouveau_mot_de_passe|' . $msg);
        }
        $d['mot_de_passe'] = password_hash($mdp, PASSWORD_DEFAULT);
    }
    $moi = utilisateur();
    if ($ancien && $ancien['type_compte'] !== 'administration') {
        // Un compte membre ne reçoit jamais de rôle d'administration
        unset($d['role_admin'], $d['prenom_admin'], $d['nom_admin']);
    } elseif (array_key_exists('role_admin', $d) && !isset(ROLES_ADMIN[(string) $d['role_admin']])) {
        throw new InvalidArgumentException("role_admin|Choisissez le rôle de ce compte d'administration.");
    }
    if ($ancien && (int) $ancien['id'] === (int) $moi['id']) {
        // On ne se retire pas à soi-même l'accès ou le rôle par erreur
        $d['actif'] = 1;
        if (isset($d['role_admin']) && $d['role_admin'] !== $ancien['role_admin']) {
            $d['role_admin'] = $ancien['role_admin'];
            flash('erreur', 'Vous ne pouvez pas modifier votre propre rôle.');
        }
    }
    if ($ancien && $ancien['role_admin'] === 'super' && array_key_exists('role_admin', $d) && $d['role_admin'] !== 'super'
        && (int) valeur("SELECT COUNT(*) FROM utilisateurs WHERE role_admin = 'super' AND actif = 1") <= 1) {
        throw new InvalidArgumentException("role_admin|Il doit rester au moins un administrateur principal.");
    }
    if (!$ancien) {
        $d['inscrit_le'] = maintenant();
        $d['type_compte'] = 'administration';
    }
}

function traiter_demandes(array $ids, string $statut): int
{
    return requete(
        'UPDATE demandes_adhesion SET statut = ?, traitee_le = ?, traitee_par = ? WHERE statut <> ? AND id IN (' . marqueurs($ids) . ')',
        array_merge([$statut, maintenant(), utilisateur()['id'], $statut], $ids)
    )->rowCount();
}
