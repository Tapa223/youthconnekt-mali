<?php
/*
 * Recherche sur tout le site : opportunités ouvertes, initiatives, actualités, communautés et pages utiles.
 * Chaque mot tapé doit se retrouver dans le contenu (accents, majuscules et pluriels ignorés) ;
 * les résultats dont le titre contient la recherche passent en premier.
 */

declare(strict_types=1);

/** Pages du site trouvées par des mots-clés courants (« contact », « ministre », « inscription »…) */
const PAGES_RECHERCHE = [
    ['/a-propos', 'À propos du chapitre', 'Mission, axes d\'intervention, mot du ministre, partenaires', 'propos chapitre mission vision histoire axe intervention ministre ministere partenaire pnud youthconnekt africa qui sommes'],
    ['/contact', 'Contact', 'Écrire à l\'équipe, adresse, téléphone, horaires', 'contact contacter ecrire message adresse telephone email mail horaire joindre question'],
    ['/compte/inscription', 'Rejoindre le chapitre', 'Créer un compte de membre individuel ou d\'organisation', 'inscription inscrire rejoindre adherer adhesion membre compte creer devenir organisation entreprise'],
    ['/compte/connexion', 'Se connecter', 'Accéder à votre espace membre', 'connexion connecter espace compte mot passe identifiant'],
    ['/temoignages', 'Témoignages', 'Ce que disent les membres et les partenaires', 'temoignage avis parole retour experience'],
    ['/communaute', 'Les communautés', 'Groupes thématiques à intégrer', 'communaute groupe reseau integrer thematique'],
    ['/opportunites', 'Toutes les opportunités', 'Formations, appels à candidatures, bourses, stages, événements', 'opportunite formation bourse stage emploi appel candidature financement concours'],
    ['/initiatives', 'Toutes les initiatives', 'Les projets du chapitre et de ses membres', 'initiative projet benevole benevolat participer volontaire'],
    ['/actualites', 'Actualités et événements', 'Les nouvelles du chapitre', 'actualite evenement nouvelle agenda forum sommet'],
    ['/confidentialite', 'Politique de confidentialité', 'Utilisation de vos données', 'confidentialite donnees personnelle vie privee rgpd'],
];

function resultats_recherche(string $q, int $par_groupe): array
{
    $groupes = [];
    if (!mots_recherche($q)) {
        return $groupes;
    }
    // Opportunités ouvertes
    [$c, $p] = condition_mots(['titre', 'resume', 'organisme', 'description', 'lieu', 'public', 'criteres', '(SELECT t.nom FROM types_opportunite t WHERE t.slug = opportunites.type)'], $q);
    [$o, $po] = pertinence_mots('titre', $q);
    $liste = opportunites_ouvertes("AND $c ORDER BY $o, date_limite IS NULL, date_limite LIMIT $par_groupe", array_merge($p, $po));
    $groupes['opportunites'] = ['Opportunités', url('/opportunites', ['q' => $q]), array_map(fn ($x) => [
        'titre' => $x['titre'], 'texte' => $x['resume'], 'url' => url('/opportunites/' . $x['slug']), 'image' => $x['image'],
        'detail' => trim(($x['type_libelle'] ?? '') . ($x['date_limite'] ? ' · jusqu\'au ' . date_chiffres($x['date_limite']) : ''), ' ·'),
    ], $liste)];
    // Initiatives
    [$c, $p] = condition_mots(['i.titre', 'i.resume', 'i.contenu', 'i.lieu', 'i.date_libelle', 'i.objectifs', 'a.nom'], $q);
    [$o, $po] = pertinence_mots('i.titre', $q);
    $liste = initiatives_publiees("AND $c ORDER BY $o, i.publie_le DESC LIMIT $par_groupe", array_merge($p, $po));
    $groupes['initiatives'] = ['Initiatives', url('/initiatives'), array_map(fn ($x) => [
        'titre' => $x['titre'], 'texte' => $x['resume'], 'url' => $x['url'], 'image' => $x['image'],
        'detail' => implode(' · ', array_filter([ETATS_INITIATIVE[$x['etat']] ?? '', $x['date_libelle'], $x['lieu']])),
    ], $liste)];
    // Actualités
    [$c, $p] = condition_mots(['titre', 'chapeau', 'contenu', 'lieu'], $q);
    [$o, $po] = pertinence_mots('titre', $q);
    $liste = actualites_publiees("AND $c ORDER BY $o, COALESCE(date_evenement, DATE(publie_le)) DESC LIMIT $par_groupe", array_merge($p, $po));
    $groupes['actualites'] = ['Actualités et événements', url('/actualites'), array_map(fn ($x) => [
        'titre' => $x['titre'], 'texte' => $x['chapeau'], 'url' => $x['url'], 'image' => $x['image'],
        'detail' => implode(' · ', array_filter([$x['categorie_libelle'], $x['date_affichee'] ? date_chiffres($x['date_affichee']) : ''])),
    ], $liste)];
    // Communautés
    $liste = array_slice(array_values(array_filter(communautes_actives(), fn ($x) =>
        texte_correspond(implode(' ', [$x['nom'], $x['public_vise'], $x['description'], $x['activites'] ?? '', $x['type_libelle']]), $q))), 0, $par_groupe);
    $groupes['communautes'] = ['Communautés', url('/communaute', ['q' => $q]), array_map(fn ($x) => [
        'titre' => $x['nom'], 'texte' => $x['public_vise'], 'url' => $x['url'], 'image' => $x['image'] ?? '',
        'detail' => $x['nb_membres'] . ' membre' . pluriel($x['nb_membres']),
    ], $liste)];
    // Pages utiles
    $pages = array_values(array_filter(PAGES_RECHERCHE, function ($pg) use ($q) {
        $cle = texte_simplifie($pg[1] . ' ' . $pg[2] . ' ' . $pg[3]);
        foreach (mots_recherche($q) as $m) {
            if (str_contains($cle, $m)) {
                return true;
            }
        }
        return false;
    }));
    $groupes['pages'] = ['Pages du site', null, array_map(fn ($pg) => ['titre' => $pg[1], 'texte' => $pg[2], 'url' => url($pg[0]), 'image' => '', 'detail' => ''], array_slice($pages, 0, 3))];
    return array_filter($groupes, fn ($g) => $g[2]);
}

/** Page de résultats */
function recherche_site(): void
{
    $q = mb_substr(trim(get('q')), 0, 100);
    $groupes = resultats_recherche($q, 12);
    afficher(vue('pages/recherche', [
        'titre' => ($q !== '' ? 'Recherche : ' . $q : 'Rechercher') . ' | YouthConnekt Mali',
        'noindex' => true,
        'q' => $q,
        'groupes' => $groupes,
        'total' => array_sum(array_map(fn ($g) => count($g[2]), $groupes)),
    ]));
}

/** Suggestions instantanées pendant la frappe (JSON) */
function recherche_suggestions(): void
{
    $q = mb_substr(trim(get('q')), 0, 100);
    $groupes = mb_strlen($q) >= 2 ? resultats_recherche($q, 4) : [];
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode([
        'q' => $q,
        'groupes' => array_values(array_map(fn ($g) => ['titre' => $g[0], 'tout' => $g[1], 'elements' => array_map(fn ($x) => [
            'titre' => $x['titre'], 'detail' => $x['detail'], 'url' => $x['url'], 'image' => $x['image'] ? media($x['image']) : '',
        ], $g[2])], $groupes)),
        'tout' => url('/recherche', ['q' => $q]),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
