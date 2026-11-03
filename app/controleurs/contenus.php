<?php
/*
 * Initiatives, opportunités, actualités et événements : listes et pages détail.
 */

declare(strict_types=1);

function initiatives(): void
{
    $axes = axes();
    $axe_actif = get('axe');
    if (!in_array($axe_actif, array_column($axes, 'slug'), true)) {
        $axe_actif = '';
    }
    $filtre = $axe_actif ? 'AND a.slug = ?' : '';
    $params = $axe_actif ? [$axe_actif] : [];

    // L'initiative à la une (page 1) n'est pas répétée dans la liste en dessous
    $une = initiatives_publiees("$filtre ORDER BY i.mis_en_avant DESC, i.publie_le DESC LIMIT 1", $params)[0] ?? null;
    if ($une) {
        $filtre .= ' AND i.id <> ?';
        $params[] = (int) $une['id'];
    }
    $total = (int) valeur("SELECT COUNT(*) FROM initiatives i LEFT JOIN axes a ON a.id = i.axe_id WHERE i.statut = 'publie' AND i.publie_le <= NOW() $filtre", $params);
    $pagination = paginer($total, 9);
    $liste = initiatives_publiees("$filtre ORDER BY i.publie_le DESC LIMIT {$pagination['par_page']} OFFSET {$pagination['decalage']}", $params);

    // Nombre d'initiatives publiées par axe, affiché sur les filtres
    $nombres = ['' => 0];
    foreach (lignes("SELECT a.slug, COUNT(*) AS n FROM initiatives i LEFT JOIN axes a ON a.id = i.axe_id WHERE i.statut = 'publie' AND i.publie_le <= NOW() GROUP BY a.slug") as $l) {
        $nombres[(string) $l['slug']] = (int) $l['n'];
        $nombres[''] += (int) $l['n'];
    }

    afficher(vue('pages/initiatives', [
        'titre' => 'Initiatives | YouthConnekt Mali',
        'description' => 'Formations, rencontres, prix : ce que le chapitre YouthConnekt Mali organise avec et pour les jeunes.',
        'axes' => $axes,
        'axe_actif' => $axe_actif,
        'une' => $une,
        'nombres' => $nombres,
        'liste' => $liste,
        'pagination' => $pagination,
    ]));
}

function initiative(string $slug): void
{
    $i = initiatives_publiees('AND i.slug = ? LIMIT 1', [$slug])[0] ?? null;
    if (!$i) {
        page_erreur(404);
    }
    afficher(vue('pages/initiative', [
        'titre' => $i['titre'] . ' | YouthConnekt Mali',
        'description' => $i['resume'],
        'i' => $i,
        'photos' => lignes('SELECT * FROM photos_initiatives WHERE initiative_id = ? ORDER BY ordre, id', [$i['id']]),
        'autres' => initiatives_publiees('AND i.id <> ? ORDER BY i.publie_le DESC LIMIT 3', [$i['id']]),
    ]));
}

function opportunites(): void
{
    $type_actif = get('type');
    if (!isset(TYPES_OPPORTUNITE[$type_actif])) {
        $type_actif = '';
    }
    $recherche = mb_substr(get('q'), 0, 100);

    $conditions = '';
    $params = [];
    if ($type_actif) {
        $conditions .= ' AND type = ?';
        $params[] = $type_actif;
    }
    if ($recherche !== '') {
        $conditions .= ' AND (titre LIKE ? OR resume LIKE ? OR organisme LIKE ?)';
        $motif = '%' . addcslashes($recherche, '%_\\') . '%';
        array_push($params, $motif, $motif, $motif);
    }
    $total = (int) valeur('SELECT COUNT(*) FROM opportunites WHERE ' . OUVERTE . $conditions, $params);
    $pagination = paginer($total, 12);

    afficher(vue('pages/opportunites', [
        'titre' => 'Opportunités | YouthConnekt Mali',
        'description' => 'Formations, appels à candidatures, bourses, stages et événements relayés par YouthConnekt Mali.',
        'type_actif' => $type_actif,
        'recherche' => $recherche,
        'total' => $total,
        'liste' => opportunites_ouvertes("$conditions ORDER BY date_limite IS NULL, date_limite, publie_le DESC LIMIT {$pagination['par_page']} OFFSET {$pagination['decalage']}", $params),
        'pagination' => $pagination,
    ]));
}

function opportunite(string $slug): void
{
    $o = ligne('SELECT * FROM opportunites WHERE ' . PUBLIE . ' AND slug = ?', [$slug]);
    if (!$o) {
        page_erreur(404);
    }
    $o = opportunite_preparer($o);
    // Opportunités similaires : même type d'abord, complétées par les autres opportunités ouvertes
    $similaires = opportunites_ouvertes('AND id <> ? ORDER BY type = ? DESC, date_limite IS NULL, date_limite LIMIT 3', [$o['id'], $o['type']]);

    afficher(vue('pages/opportunite', [
        'titre' => $o['titre'] . ' | Opportunités | YouthConnekt Mali',
        'description' => $o['resume'],
        'o' => $o,
        'similaires' => $similaires,
    ]));
}

function actualites(): void
{
    $filtre = get('filtre');
    if (!in_array($filtre, ['evenements', 'actualites'], true)) {
        $filtre = '';
    }
    $une = actualites_publiees('ORDER BY mis_en_avant DESC, publie_le DESC LIMIT 1')[0] ?? null;
    $agenda = actualites_publiees("AND categorie = 'evenement' AND date_evenement >= CURDATE() ORDER BY date_evenement LIMIT 4");

    $donnees = [
        'titre' => 'Actualités | YouthConnekt Mali',
        'description' => 'Les actualités et les événements de YouthConnekt Mali.',
        'filtre' => $filtre,
        'une' => $une,
        'agenda' => $agenda,
        'evenements' => null,
        'liste' => null,
        'pagination' => null,
    ];
    if ($filtre !== 'actualites') {
        // Événements à venir d'abord, puis les plus récents parmi les passés
        $a_venir = actualites_publiees("AND categorie = 'evenement' AND date_evenement >= CURDATE() ORDER BY date_evenement LIMIT 6");
        $passes = actualites_publiees("AND categorie = 'evenement' AND (date_evenement IS NULL OR date_evenement < CURDATE()) ORDER BY date_evenement DESC, publie_le DESC LIMIT 6");
        $donnees['evenements'] = array_slice(array_merge($a_venir, $passes), 0, 6);
    }
    if ($filtre !== 'evenements') {
        $total = (int) valeur('SELECT COUNT(*) FROM actualites WHERE ' . PUBLIE . " AND categorie = 'actualite'");
        $pagination = paginer($total, 9);
        $donnees['pagination'] = $pagination;
        $donnees['liste'] = actualites_publiees("AND categorie = 'actualite' ORDER BY publie_le DESC LIMIT {$pagination['par_page']} OFFSET {$pagination['decalage']}");
    }
    afficher(vue('pages/actualites', $donnees));
}

function actualite(string $slug): void
{
    $a = ligne('SELECT * FROM actualites WHERE ' . PUBLIE . ' AND slug = ?', [$slug]);
    if (!$a) {
        page_erreur(404);
    }
    $a = actualite_preparer($a);
    afficher(vue('pages/actualite', [
        'titre' => $a['titre'] . ' | YouthConnekt Mali',
        'description' => $a['chapeau'],
        'a' => $a,
        'photos' => lignes('SELECT * FROM photos_actualites WHERE actualite_id = ? ORDER BY ordre, id', [$a['id']]),
        'autres' => actualites_publiees('AND id <> ? ORDER BY publie_le DESC LIMIT 3', [$a['id']]),
    ]));
}
