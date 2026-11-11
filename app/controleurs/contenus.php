<?php
/*
 * Initiatives, opportunités, actualités et événements : listes et pages détail.
 */

declare(strict_types=1);

function initiatives(): void
{
    // Filtre par état (à venir, en cours, terminées) : ce qui aide un jeune à choisir
    $etat_actif = get('etat');
    if (!isset(ETATS_INITIATIVE[$etat_actif])) {
        $etat_actif = '';
    }
    $filtre = $etat_actif ? 'AND i.etat = ?' : '';
    $params = $etat_actif ? [$etat_actif] : [];

    $total = (int) valeur("SELECT COUNT(*) FROM initiatives i WHERE i.statut = 'publie' AND i.publie_le <= NOW() $filtre", $params);
    $pagination = paginer($total, 9);
    $liste = initiatives_publiees("$filtre ORDER BY i.publie_le DESC LIMIT {$pagination['par_page']} OFFSET {$pagination['decalage']}", $params);

    $nombres = ['' => 0];
    foreach (lignes("SELECT i.etat, COUNT(*) AS n FROM initiatives i WHERE i.statut = 'publie' AND i.publie_le <= NOW() GROUP BY i.etat") as $l) {
        $nombres[(string) $l['etat']] = (int) $l['n'];
        $nombres[''] += (int) $l['n'];
    }

    afficher(vue('pages/initiatives', [
        'titre' => 'Initiatives | YouthConnekt Mali',
        'description' => 'Les projets du chapitre YouthConnekt Mali et de ses membres, auxquels les jeunes peuvent prendre part.',
        'etat_actif' => $etat_actif,
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
    $roles = roles_initiative((int) $i['id']);
    $u = utilisateur();
    afficher(vue('pages/initiative', [
        'roles' => $roles,
        'ouvertes' => participations_ouvertes($i, $roles),
        'inscrits' => inscrits_initiative((int) $i['id']),
        'ma_participation' => $u ? ligne('SELECT p.*, COALESCE(r.nom, p.role_nom) AS role_libelle FROM participations p LEFT JOIN initiative_roles r ON r.id = p.role_id WHERE p.initiative_id = ? AND p.utilisateur_id = ?', [$i['id'], $u['id']]) : null,
        'titre' => $i['titre'] . ' | YouthConnekt Mali',
        'description' => $i['resume'],
        'image_partage' => $i['image'],
        'i' => $i,
        'photos' => lignes('SELECT * FROM photos_initiatives WHERE initiative_id = ? ORDER BY ordre, id', [$i['id']]),
        'autres' => initiatives_publiees('AND i.id <> ? ORDER BY i.publie_le DESC LIMIT 3', [$i['id']]),
    ]));
}

function opportunites(): void
{
    $type_actif = get('type');
    $types = types_opportunite();
    if (!isset($types[$type_actif])) {
        $type_actif = '';
    }
    $recherche = mb_substr(get('q'), 0, 100);

    $conditions = '';
    $params = [];
    if ($type_actif) {
        $conditions .= ' AND type = ?';
        $params[] = $type_actif;
    }
    $ordre = 'date_limite IS NULL, date_limite, publie_le DESC';
    $params_ordre = [];
    if (mots_recherche($recherche)) {
        // Chaque mot est cherché partout dans l'opportunité, y compris le nom de son type (« bourse », « stage »…)
        [$c, $p] = condition_mots(['titre', 'resume', 'organisme', 'description', 'lieu', 'public', 'criteres', '(SELECT t.nom FROM types_opportunite t WHERE t.slug = opportunites.type)'], $recherche);
        $conditions .= " AND $c";
        $params = array_merge($params, $p);
        [$pertinence, $params_ordre] = pertinence_mots('titre', $recherche);
        $ordre = "$pertinence, $ordre";
    }
    $total = (int) valeur('SELECT COUNT(*) FROM opportunites WHERE ' . OUVERTE . $conditions, $params);
    $pagination = paginer($total, 12);

    // Nombre d'opportunités ouvertes par type, pour la liste de choix
    $nombres = [];
    foreach (lignes('SELECT type, COUNT(*) AS n FROM opportunites WHERE ' . OUVERTE . ' GROUP BY type') as $l) {
        $nombres[$l['type']] = (int) $l['n'];
    }

    afficher(vue('pages/opportunites', [
        'titre' => 'Opportunités | YouthConnekt Mali',
        'description' => 'Formations, appels à candidatures, bourses, stages et événements relayés par YouthConnekt Mali.',
        'type_actif' => $type_actif,
        'types' => $types,
        'nombres' => $nombres,
        'recherche' => $recherche,
        'total' => $total,
        'liste' => opportunites_ouvertes("$conditions ORDER BY $ordre LIMIT {$pagination['par_page']} OFFSET {$pagination['decalage']}", array_merge($params, $params_ordre)),
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
        'image_partage' => $o['image'],
        'o' => $o,
        'similaires' => $similaires,
    ]));
}

function actualites(): void
{
    $categories = categories_actualite();
    $categorie = get('categorie');
    if (!isset($categories[$categorie])) {
        $categorie = '';
    }
    $condition = $categorie !== '' ? ' AND categorie = ?' : '';
    $params = $categorie !== '' ? [$categorie] : [];
    // Plus récentes d'abord : la date de l'événement (à venir comprise) ou, à défaut, la date de publication
    $ordre = 'ORDER BY COALESCE(date_evenement, DATE(publie_le)) DESC, publie_le DESC';

    // Nombre d'actualités par catégorie, pour le menu (seules les catégories utilisées y figurent)
    $nombres = [];
    foreach (lignes('SELECT categorie, COUNT(*) AS n FROM actualites WHERE ' . PUBLIE . " AND categorie <> '' GROUP BY categorie") as $l) {
        if (isset($categories[$l['categorie']])) {
            $nombres[$l['categorie']] = (int) $l['n'];
        }
    }

    // À la une : les actualités cochées « À la une » dans l'administration (quatre au plus).
    // Aucune cochée : la page commence directement par toutes les actualités. Elles sont en haut de la page, puis retirées de la liste en dessous.
    $une = [];
    if ($categorie === '') {
        $une = actualites_publiees('AND mis_en_avant = 1 ORDER BY publie_le DESC LIMIT 4');
    }
    $exclure = $une ? ' AND id NOT IN (' . implode(',', array_map(fn ($a) => (int) $a['id'], $une)) . ')' : '';
    $pagination = paginer((int) valeur('SELECT COUNT(*) FROM actualites WHERE ' . PUBLIE . $condition . $exclure, $params), 12);
    $liste = actualites_publiees($condition . $exclure . " $ordre LIMIT {$pagination['par_page']} OFFSET {$pagination['decalage']}", $params);
    $suivante = $pagination['page'] < $pagination['pages'] ? avec_parametres(['page' => $pagination['page'] + 1]) : null;

    // Défilement continu : le navigateur demande la suite de la liste au fur et à mesure
    if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'html' => implode('', array_map(fn ($a) => '<li>' . rendre('cartes/actualite', ['a' => $a, 'mode' => 'liste']) . '</li>', $liste)),
            'suivante' => $suivante,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($pagination['page'] > 1) {
        $une = [];
    }

    afficher(vue('pages/actualites', [
        'titre' => 'Actualités | YouthConnekt Mali',
        'description' => 'Les actualités et les événements de YouthConnekt Mali.',
        'categories' => $categories,
        'categorie_active' => $categorie,
        'nombres' => $nombres,
        'total' => (int) valeur('SELECT COUNT(*) FROM actualites WHERE ' . PUBLIE),
        'une' => $une,
        'liste' => $liste,
        'suivante' => $suivante,
    ]));
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
        'image_partage' => $a['image'],
        'a' => $a,
        'photos' => lignes('SELECT * FROM photos_actualites WHERE actualite_id = ? ORDER BY ordre, id', [$a['id']]),
        'autres' => actualites_publiees('AND id <> ? ORDER BY publie_le DESC LIMIT 3', [$a['id']]),
    ]));
}

/**
 * Proposition d'une opportunité par un membre (individuel ou organisation).
 * Elle est enregistrée « à valider » : l'administration la relit puis la publie.
 */
function proposer_opportunite(): void
{
    $ajax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
    $u = utilisateur();
    $repondre = function (array $erreurs, string $texte = '') use ($ajax) {
        if ($ajax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($erreurs ? ['ok' => false, 'erreurs' => $erreurs] : ['ok' => true, 'message' => $texte], JSON_UNESCAPED_UNICODE);
            exit;
        }
        flash($erreurs ? 'erreur' : 'succes', $erreurs ? implode(' ', $erreurs) : $texte);
        rediriger(url('/opportunites'));
    };
    if (!$u || !est_membre($u)) {
        $repondre(['compte' => 'Connectez-vous avec votre compte membre pour proposer une opportunité.']);
    }

    $v = [];
    foreach (['titre', 'type', 'organisme', 'date_limite', 'lieu', 'lien_candidature', 'description'] as $c) {
        $v[$c] = post($c);
    }
    $erreurs = [];
    if ($v['titre'] === '') {
        $erreurs['titre'] = "Indiquez le titre de l'opportunité.";
    } elseif (mb_strlen($v['titre']) > 200) {
        $erreurs['titre'] = 'Titre trop long : 200 caractères au maximum.';
    }
    if (!isset(types_opportunite()[$v['type']])) {
        $erreurs['type'] = 'Choisissez le type de l\'opportunité.';
    }
    if ($v['date_limite'] !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $v['date_limite']);
        if (!$d || $d->format('Y-m-d') !== $v['date_limite']) {
            $erreurs['date_limite'] = 'La date limite n\'est pas valide.';
        } elseif ($v['date_limite'] < date('Y-m-d')) {
            $erreurs['date_limite'] = 'La date limite est déjà passée.';
        }
    }
    if ($v['lien_candidature'] !== '') {
        if (!preg_match('#^https?://#i', $v['lien_candidature'])) {
            $v['lien_candidature'] = 'https://' . $v['lien_candidature'];
        }
        if (!filter_var($v['lien_candidature'], FILTER_VALIDATE_URL)) {
            $erreurs['lien_candidature'] = 'Le lien de candidature n\'est pas valide.';
        }
    }
    if (mb_strlen($v['description']) < 30) {
        $erreurs['description'] = $v['description'] === '' ? "Décrivez l'opportunité." : 'Décrivez un peu plus l\'opportunité (30 caractères au minimum).';
    }
    $image = null;
    if (!$erreurs) {
        try {
            $image = televerser_image($_FILES['image'] ?? null, 'opportunites');
        } catch (RuntimeException $e) {
            $erreurs['image'] = $e->getMessage();
        }
    }
    if ($erreurs) {
        $repondre($erreurs);
    }

    $resume = trim(preg_replace('/\s+/u', ' ', $v['description']));
    $id = inserer('opportunites', [
        'titre' => $v['titre'],
        'slug' => slug_unique('opportunites', $v['titre']),
        'statut' => 'propose',
        'publie_le' => maintenant(),
        'type' => $v['type'],
        'propose_par' => (int) $u['id'],
        'organisme' => mb_substr($v['organisme'] !== '' ? $v['organisme'] : ($u['type_compte'] === 'organisation' ? $u['nom_affiche'] : ''), 0, 150),
        'resume' => mb_strlen($resume) > 280 ? mb_substr($resume, 0, 277) . '…' : $resume,
        'description' => mb_substr($v['description'], 0, 8000),
        'lieu' => mb_substr($v['lieu'], 0, 120),
        'date_limite' => $v['date_limite'] !== '' ? $v['date_limite'] : null,
        'lien_candidature' => mb_substr($v['lien_candidature'], 0, 255),
        'image' => $image ?? '',
        'cree_le' => maintenant(),
        'modifie_le' => maintenant(),
    ]);
    $copie = (string) config('email.copie_contact', '');
    if ($copie !== '') {
        envoyer_email($copie, 'Opportunité proposée : ' . $v['titre'],
            "{$u['nom_affiche']} <{$u['email']}> propose une opportunité.\nÀ relire et publier : " . url_absolue(url('/gestion/opportunites/' . $id)), '', $u['email']);
    }
    $repondre([], "Merci ! Votre opportunité a bien été envoyée. Elle sera publiée après validation par l'équipe du chapitre.");
}

/** Fichier d'agenda (.ics) d'une initiative datée : s'ouvre dans Google Agenda, Outlook ou le téléphone. */
function initiative_agenda(string $slug): void
{
    $i = initiatives_publiees('AND i.slug = ? LIMIT 1', [$slug])[0] ?? null;
    if (!$i || !$i['date_debut']) {
        page_erreur(404);
    }
    envoyer_agenda('initiative-' . $i['id'], $i['titre'], $i['date_debut'], $i['date_fin'] ?: $i['date_debut'], (string) $i['lieu'], (string) $i['resume'], url_absolue('/initiatives/' . $i['slug']), $i['slug']);
}

function actualite_agenda(string $slug): void
{
    $a = actualites_publiees('AND slug = ? LIMIT 1', [$slug])[0] ?? null;
    if (!$a || !$a['date_evenement']) {
        page_erreur(404);
    }
    envoyer_agenda('actualite-' . $a['id'], $a['titre'], $a['date_evenement'], $a['date_evenement'], (string) $a['lieu'], (string) $a['chapeau'], url_absolue('/actualites/' . $a['slug']), $a['slug']);
}
