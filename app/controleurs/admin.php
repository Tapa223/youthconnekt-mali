<?php
/*
 * Administration : tableau de bord, recherche, et moteur commun des listes et formulaires
 * (les rubriques sont décrites dans app/ressources.php).
 */

declare(strict_types=1);

require_once RACINE . '/app/admin_outils.php';
require_once RACINE . '/app/ressources.php';

function vue_admin(string $nom, array $donnees): never
{
    $u = utilisateur();
    afficher(vue($nom, $donnees + [
        'admin' => $u,
        'menu' => menu_admin(),
        'titre' => ($donnees['titre_page'] ?? 'Administration') . ' | Administration YouthConnekt Mali',
    ], 'admin'));
}

function ressource_ou_404(string $slug): array
{
    $r = ressource($slug);
    if (!$r) {
        page_erreur(404);
    }
    return $r;
}

// ---------------------------------------------------------------------------
// Tableau de bord
// ---------------------------------------------------------------------------

function tableau_de_bord(): void
{
    $u = exiger_admin();
    $debut_mois = date('Y-m-01 00:00:00');
    $fin_mois = date('Y-m-01', strtotime('first day of next month'));
    $cartes = [];
    $voit_membres = peut('membres') || peut('comptes');

    if ($voit_membres) {
        $cartes[] = [
            'libelle' => 'Membres inscrits', 'couleur' => 'vert', 'icone' => ICONES['personne'], 'url' => url('/gestion/membres'),
            'valeur' => (int) valeur('SELECT COUNT(*) FROM utilisateurs WHERE type_compte <> \'administration\''),
            'detail' => '+' . (int) valeur('SELECT COUNT(*) FROM utilisateurs WHERE type_compte <> \'administration\' AND inscrit_le >= ?', [$debut_mois]) . ' ce mois-ci',
        ];
    }
    if (peut('opportunites')) {
        $closent = (int) valeur('SELECT COUNT(*) FROM opportunites WHERE ' . PUBLIE . ' AND date_limite >= CURDATE() AND date_limite < ?', [$fin_mois]);
        $cartes[] = [
            'libelle' => 'Opportunités publiées', 'couleur' => 'orange', 'icone' => ICONES['mallette'], 'url' => url('/gestion/opportunites'),
            'valeur' => (int) valeur('SELECT COUNT(*) FROM opportunites WHERE ' . PUBLIE),
            'detail' => $closent . ' clôture' . ($closent > 1 ? 'nt' : '') . ' ce mois-ci',
        ];
    }
    if (peut('messages')) {
        $dernier = valeur('SELECT MAX(recu_le) FROM messages_contact WHERE traite = 0');
        $cartes[] = [
            'libelle' => 'Messages non lus', 'couleur' => 'turquoise', 'icone' => ICONES['enveloppe'], 'url' => url('/gestion/messages', ['onglet' => 1]),
            'valeur' => (int) valeur('SELECT COUNT(*) FROM messages_contact WHERE traite = 0'),
            'detail' => $dernier ? 'Dernier le ' . date('d/m à H \h i', strtotime($dernier)) : 'Aucun en attente',
        ];
    }
    if (peut('newsletter')) {
        $cartes[] = [
            'libelle' => 'Abonnés newsletter', 'couleur' => 'bleu', 'icone' => ICONES['lettre'], 'url' => url('/gestion/newsletter'),
            'valeur' => (int) valeur('SELECT COUNT(*) FROM abonnes_newsletter WHERE actif = 1'),
            'detail' => '+' . (int) valeur('SELECT COUNT(*) FROM abonnes_newsletter WHERE actif = 1 AND inscrit_le >= ?', [$debut_mois]) . ' ce mois-ci',
        ];
    }

    $inscriptions = [];
    if ($voit_membres) {
        foreach (lignes('SELECT * FROM utilisateurs WHERE type_compte <> \'administration\' ORDER BY inscrit_le DESC LIMIT 5') as $m) {
            $m = completer_utilisateur($m);
            $p = $m['profil'];
            $organisation = $m['type_compte'] === 'organisation';
            $inscriptions[] = [
                'nom' => $m['nom_affiche'], 'initiales' => $m['initiales'],
                'profil' => $organisation ? 'Organisation' : 'Membre individuel',
                'region' => $p ? (REGIONS[$p['region']] ?? '—') : '—',
                'date' => $m['inscrit_le'],
                'url' => $p ? url('/gestion/' . ($organisation ? 'organisations' : 'membres') . '/' . $p['id']) : url('/gestion/comptes/' . $m['id']),
            ];
        }
    }

    $bientot = [];
    if (peut('opportunites')) {
        foreach (lignes('SELECT id, titre, date_limite FROM opportunites WHERE ' . OUVERTE . ' AND date_limite IS NOT NULL ORDER BY date_limite LIMIT 3') as $o) {
            $bientot[] = ['titre' => $o['titre'], 'jours' => jours_restants($o['date_limite']), 'date' => $o['date_limite'], 'url' => url('/gestion/opportunites/' . $o['id'])];
        }
    }

    $actions = [];
    foreach ([['opportunites', 'Ajouter une opportunité', 'mallette'], ['actualites', 'Publier une actualité', 'journal'],
        ['initiatives', 'Ajouter une initiative', 'drapeau'], ['partenaires', 'Ajouter un partenaire', 'batiment'],
        ['temoignages', 'Ajouter un témoignage', 'bulle']] as [$rub, $libelle, $icone]) {
        if (peut($rub, 'modifier')) {
            $actions[] = ['libelle' => $libelle, 'url' => url('/gestion/' . $rub . '/ajouter'), 'icone' => ICONES[$icone]];
        }
    }

    // Participation des jeunes : ce que les membres apportent eux-mêmes au chapitre
    $participation = [];
    $contributeurs = [];
    if (peut('opportunites') || peut('messages') || peut('temoignages')) {
        $nb = fn (string $sql) => (int) (function () use ($sql) { try { return valeur($sql); } catch (Throwable $e) { return 0; } })();
        $opp = $nb('SELECT COUNT(*) FROM opportunites WHERE propose_par IS NOT NULL');
        $opp_pub = $nb("SELECT COUNT(*) FROM opportunites WHERE propose_par IS NOT NULL AND statut = 'publie'");
        $init = $nb("SELECT COUNT(*) FROM messages_contact WHERE objet LIKE 'Proposition d''initiative%'");
        $tem = $nb('SELECT COUNT(*) FROM temoignages WHERE utilisateur_id IS NOT NULL');
        $tem_pub = $nb('SELECT COUNT(*) FROM temoignages WHERE utilisateur_id IS NOT NULL AND publie = 1');
        $adh = $nb("SELECT COUNT(*) FROM demandes_adhesion WHERE statut = 'acceptee'");
        $participation = [
            ['Opportunités proposées', $opp, $opp_pub . ' publiée' . pluriel($opp_pub), url('/gestion/propositions')],
            ['Initiatives présentées', $init, 'par les jeunes et les organisations', url('/gestion/messages', ['q' => "Proposition d'initiative"])],
            ['Témoignages envoyés', $tem, $tem_pub . ' affiché' . pluriel($tem_pub), url('/gestion/temoignages')],
            ['Adhésions aux communautés', $adh, 'membres acceptés', url('/gestion/communautes')],
        ];
        try {
            $contributeurs = lignes(
                "SELECT u.*, (SELECT COUNT(*) FROM opportunites o WHERE o.propose_par = u.id)
                   + (SELECT COUNT(*) FROM temoignages t WHERE t.utilisateur_id = u.id)
                   + (SELECT COUNT(*) FROM messages_contact m WHERE m.email = u.email AND m.objet LIKE 'Proposition d''initiative%') AS nb
                 FROM utilisateurs u WHERE u.type_compte <> 'administration' HAVING nb > 0 ORDER BY nb DESC LIMIT 5"
            );
            $contributeurs = array_map(fn ($m) => completer_utilisateur($m) + ['nb' => (int) $m['nb']], $contributeurs);
        } catch (Throwable $e) {
            $contributeurs = [];
        }
    }

    vue_admin('admin/tableau_de_bord', [
        'titre_page' => 'Tableau de bord',
        'participation' => $participation,
        'contributeurs' => $contributeurs,
        'ariane' => [],
        'cartes' => $cartes,
        'inscriptions' => $inscriptions,
        'url_membres' => $voit_membres ? url('/gestion/membres') : null,
        'bientot' => $bientot,
        'actions' => $actions,
        'chiffres' => peut('chiffres', 'modifier') ? lignes('SELECT * FROM chiffres_cles ORDER BY ordre, id LIMIT 4') : [],
        'prenom' => $u['prenom_affiche'],
    ]);
}

function chiffres_rapides(): void
{
    exiger_droit('chiffres', 'modifier');
    $n = 0;
    foreach (lignes('SELECT id, valeur FROM chiffres_cles') as $c) {
        $v = mb_substr(post('chiffre_' . $c['id']), 0, 30);
        if ($v !== '' && $v !== $c['valeur']) {
            requete('UPDATE chiffres_cles SET valeur = ? WHERE id = ?', [$v, $c['id']]);
            $n++;
        }
    }
    flash('succes', "Chiffres clés enregistrés ($n modifié" . ($n > 1 ? 's' : '') . ').');
    rediriger(url('/gestion'));
}

/** Liste des chiffres clés : valeurs, ordre et publication modifiables directement. */
function chiffres_enregistrer(): void
{
    exiger_droit('chiffres', 'modifier');
    foreach (lignes('SELECT id FROM chiffres_cles') as $c) {
        $id = (int) $c['id'];
        $valeur = mb_substr(trim((string) ($_POST['valeur'][$id] ?? '')), 0, 30);
        if ($valeur === '') {
            continue;
        }
        requete('UPDATE chiffres_cles SET valeur = ?, ordre = ?, publie = ? WHERE id = ?', [
            $valeur, max(0, (int) ($_POST['ordre'][$id] ?? 0)), isset($_POST['publie'][$id]) ? 1 : 0, $id,
        ]);
    }
    flash('succes', 'Chiffres clés enregistrés.');
    rediriger(url('/gestion/chiffres'));
}

function recherche(): void
{
    exiger_admin();
    $q = mb_substr(get('q'), 0, 100);
    $groupes = [];
    if (mb_strlen($q) >= 2) {
        foreach (['opportunites', 'actualites', 'initiatives', 'membres', 'organisations', 'communautes', 'messages', 'newsletter', 'comptes'] as $slug) {
            $r = ressource($slug);
            if (!peut($r['rubrique']) || !$r['recherche']) {
                continue;
            }
            [$where, $params] = condition_recherche($r, $q);
            $lignes = lignes("SELECT {$r['champs_select']} {$r['depuis']} WHERE $where LIMIT 8", $params);
            if ($lignes) {
                $groupes[] = [
                    'titre' => $r['titre'],
                    'elements' => array_map(fn ($l) => [
                        'texte' => ($r['nom_element'])($l),
                        'url' => $slug === 'messages' ? url('/gestion/messages', ['ouvert' => $l['id']]) : url('/gestion/' . $slug . '/' . $l['id']),
                    ], $lignes),
                    'tout' => url('/gestion/' . $slug, ['q' => $q]),
                ];
            }
        }
    }
    vue_admin('admin/recherche', ['titre_page' => 'Recherche', 'ariane' => [['Recherche', null]], 'q' => $q, 'groupes' => $groupes]);
}

function exemples(): void
{
    exiger_admin();
    if (utilisateur()['role_admin'] !== 'super') {
        page_erreur(403);
    }
    require_once RACINE . '/app/exemples.php';
    if (post('quoi') === 'retirer') {
        $n = retirer_exemples();
        flash('succes', "Contenus d'exemple retirés ($n élément" . pluriel($n) . ').');
    } else {
        charger_exemples();
        flash('succes', "Contenus d'exemple chargés. Pensez à les retirer avant la mise en ligne officielle.");
    }
    rediriger(url('/gestion/parametres'));
}

// ---------------------------------------------------------------------------
// Rubriques particulières
// ---------------------------------------------------------------------------

function message_traiter(int $id): void
{
    exiger_droit('messages', 'modifier');
    $traite = post('traite') === '1' ? 1 : 0;
    requete('UPDATE messages_contact SET traite = ? WHERE id = ?', [$traite, $id]);
    flash('succes', $traite ? 'Message marqué comme traité.' : 'Message remis à traiter.');
    rediriger(adresse_retour(url('/gestion/messages')));
}

function abonne_basculer(int $id): void
{
    exiger_droit('newsletter', 'modifier');
    $a = ligne('SELECT * FROM abonnes_newsletter WHERE id = ?', [$id]);
    if ($a) {
        requete('UPDATE abonnes_newsletter SET actif = ? WHERE id = ?', [$a['actif'] ? 0 : 1, $id]);
        flash('succes', $a['email'] . ' : ' . ($a['actif'] ? 'désabonné.' : 'réabonné.'));
    }
    rediriger(adresse_retour(url('/gestion/newsletter')));
}

function demande(int $id): void
{
    exiger_droit('demandes');
    $r = ressource('demandes');
    $d = ligne("SELECT {$r['champs_select']}, c.slug AS communaute_slug, tp.email AS traitee_par_email {$r['depuis']}
        LEFT JOIN utilisateurs tp ON tp.id = t.traitee_par WHERE t.id = ?", [$id]);
    if (!$d) {
        page_erreur(404);
    }
    if (est_post()) {
        exiger_droit('demandes', 'modifier');
        $statut = post('decision') === 'accepter' ? 'acceptee' : 'refusee';
        traiter_demandes([$id], $statut);
        flash('succes', 'Demande de ' . $d['membre_nom'] . ($statut === 'acceptee' ? ' acceptée.' : ' refusée.'));
        rediriger(url('/gestion/communautes'));
    }
    $membre = completer_utilisateur(ligne('SELECT * FROM utilisateurs WHERE id = ?', [$d['utilisateur_id']]));
    vue_admin('admin/demande', [
        'titre_page' => "Demande d'intégration",
        'ariane' => [['Communautés', url('/gestion/communautes')], [$d['membre_nom'], null]],
        'd' => $d,
        'membre' => $membre,
        'peut_modifier' => peut('demandes', 'modifier'),
    ]);
}

// ---------------------------------------------------------------------------
// Listes
// ---------------------------------------------------------------------------

function condition_recherche(array $r, string $q): array
{
    $motif = '%' . addcslashes($q, '%_\\') . '%';
    $morceaux = array_map(fn ($c) => "$c LIKE ?", $r['recherche']);
    return ['(' . implode(' OR ', $morceaux) . ')', array_fill(0, count($morceaux), $motif)];
}

function options_filtre($options): array
{
    return is_string($options) ? $options() : $options;
}

function liste(string $slug): void
{
    $r = ressource_ou_404($slug);
    exiger_droit($r['rubrique']);
    if ($r['fiche_unique']) {
        rediriger(url('/gestion/' . $slug . '/1'));
    }

    // Conditions : onglet, filtres, recherche
    $conditions = [$r['condition']];
    $params = [];
    $onglet = (int) get('onglet');
    if ($onglet > 0 && isset($r['onglets'][$onglet - 1])) {
        $conditions[] = $r['onglets'][$onglet - 1][1];
    } else {
        $onglet = 0;
    }
    $filtres = [];
    foreach ($r['filtres'] as [$param, $titre, $options, $colonne]) {
        $options = options_filtre($options);
        $choisi = get($param);
        if ($choisi !== '' && isset($options[$choisi])) {
            $conditions[] = "$colonne = ?";
            $params[] = $choisi;
        } else {
            $choisi = '';
        }
        $choix = [['libelle' => $titre, 'url' => avec_parametres([$param => null, 'p' => null]), 'choisi' => $choisi === '']];
        foreach ($options as $cle => $libelle) {
            $choix[] = ['libelle' => $libelle, 'url' => avec_parametres([$param => $cle, 'p' => null]), 'choisi' => (string) $choisi === (string) $cle];
        }
        $filtres[] = ['titre' => $titre, 'options' => $choix];
    }
    $q = mb_substr(get('q'), 0, 100);
    if ($q !== '' && $r['recherche']) {
        [$c, $p] = condition_recherche($r, $q);
        $conditions[] = $c;
        $params = array_merge($params, $p);
    }
    $where = implode(' AND ', $conditions);

    // Export CSV de la liste filtrée
    if (get('export') === 'csv' && $r['export']) {
        exporter_csv($r, lignes("SELECT {$r['champs_select']} {$r['depuis']} WHERE $where ORDER BY {$r['ordre']}", $params));
    }

    // Onglets avec leur nombre d'éléments
    $onglets = [];
    if ($r['onglets']) {
        $onglets[] = ['libelle' => $r['libelle_tous'], 'nombre' => (int) valeur("SELECT COUNT(*) {$r['depuis']} WHERE {$r['condition']}"), 'url' => url('/gestion/' . $slug), 'actif' => $onglet === 0];
        foreach ($r['onglets'] as $i => [$libelle, $condition]) {
            $onglets[] = [
                'libelle' => $libelle,
                'nombre' => (int) valeur("SELECT COUNT(*) {$r['depuis']} WHERE {$r['condition']} AND $condition"),
                'url' => url('/gestion/' . $slug, ['onglet' => $i + 1]),
                'actif' => $onglet === $i + 1,
            ];
        }
    }

    $total = (int) valeur("SELECT COUNT(*) {$r['depuis']} WHERE $where", $params);
    $pages = max(1, (int) ceil($total / $r['par_page']));
    $page = max(1, min($pages, (int) get('p', '1')));
    $lignes = lignes("SELECT {$r['champs_select']} {$r['depuis']} WHERE $where ORDER BY {$r['ordre']} LIMIT {$r['par_page']} OFFSET " . (($page - 1) * $r['par_page']), $params);

    $donnees = [
        'titre_page' => $r['titre'],
        'ariane' => array_merge($r['parent'] ? [$r['parent']] : [], [[$r['titre'], null]]),
        'r' => $r,
        'lignes' => $lignes,
        'onglets' => $onglets,
        'filtres' => $filtres,
        'q' => $q,
        'total' => $total,
        'page' => $page,
        'pages' => $pages,
        'url_export' => $r['export'] ? avec_parametres(['export' => 'csv', 'p' => null]) : null,
        'peut_modifier' => peut($r['rubrique'], 'modifier'),
        // La barre du haut cherche dans cette liste, en gardant l'onglet et les filtres choisis
        'recherche_liste' => $r['recherche'] ? [
            'action' => url('/gestion/' . $slug),
            'q' => $q,
            'garder' => array_filter(array_intersect_key($_GET, array_flip(array_merge(['onglet'], array_column($r['filtres'], 0)))), 'is_string'),
        ] : null,
    ];

    // Compléments propres à certaines rubriques
    if ($slug === 'communautes') {
        $donnees['voit_demandes'] = peut('demandes');
        $rd = ressource('demandes');
        $donnees['demandes'] = peut('demandes')
            ? lignes("SELECT {$rd['champs_select']}, pi.categorie, pi.region AS region_i, po.type_structure, po.region AS region_o {$rd['depuis']} WHERE t.statut = 'en_attente' ORDER BY t.demandee_le DESC LIMIT 20")
            : [];
        $donnees['total_attente'] = (int) valeur("SELECT COUNT(*) FROM demandes_adhesion WHERE statut = 'en_attente'");
        $donnees['acceptation_auto'] = (bool) parametres()['acceptation_automatique'];
    }
    if ($slug === 'messages') {
        $ouvert = (int) get('ouvert');
        $donnees['ouvert'] = $ouvert ? ligne('SELECT * FROM messages_contact WHERE id = ?', [$ouvert]) : ($lignes[0] ?? null);
    }
    vue_admin($r['vue_liste'], $donnees);
}

function exporter_csv(array $r, array $lignes): never
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $r['slug'] . '-' . date('Y-m-d') . '.csv"');
    $sortie = fopen('php://output', 'w');
    fwrite($sortie, "\xEF\xBB\xBF"); // pour qu'Excel lise correctement les accents
    fputcsv($sortie, array_keys($r['export']), ';');
    foreach ($lignes as $l) {
        $valeurs = [];
        foreach ($r['export'] as $source) {
            $v = is_callable($source) ? $source($l) : ($l[$source] ?? '');
            // Une cellule qui commence par =, +, - ou @ pourrait être prise pour une formule
            $v = (string) $v;
            if ($v !== '' && strpos('=+-@', $v[0]) !== false) {
                $v = "'" . $v;
            }
            $valeurs[] = $v;
        }
        fputcsv($sortie, $valeurs, ';');
    }
    fclose($sortie);
    exit;
}

function action_groupee(string $slug): void
{
    $r = ressource_ou_404($slug);
    exiger_droit($r['rubrique'], 'modifier');
    $action = post('action');
    $ids = array_values(array_unique(array_filter(array_map('intval', (array) ($_POST['ids'] ?? [])))));
    if (!isset($r['actions'][$action])) {
        flash('erreur', 'Choisissez une action dans la liste.');
    } elseif (!$ids) {
        flash('erreur', 'Cochez au moins un élément de la liste.');
    } else {
        flash('succes', ($r['actions'][$action][1])($ids));
    }
    rediriger(adresse_retour(url('/gestion/' . $slug)));
}

// ---------------------------------------------------------------------------
// Formulaires
// ---------------------------------------------------------------------------

function groupes_de(array $r, ?array $ligne): array
{
    return is_string($r['groupes']) ? ($r['groupes'])($ligne) : $r['groupes'];
}

function champs_de(array $groupes): array
{
    $champs = [];
    foreach ($groupes as $g) {
        foreach ($g['champs'] as $nom => $def) {
            $champs[$nom] = $def + ['requis' => true, 'libelle' => $nom, 'max' => null];
        }
    }
    return $champs;
}

function formulaire(string $slug, ?int $id = null): void
{
    $r = ressource_ou_404($slug);
    if (!$r['groupes']) {
        page_erreur(404);
    }
    exiger_droit($r['rubrique']);
    $peut_modifier = peut($r['rubrique'], 'modifier');
    $table = $r['table'];

    if ($r['fiche_unique']) {
        $id = 1;
        requete("INSERT IGNORE INTO `$table` (id) VALUES (1)");
    }
    $ligne = null;
    if ($id !== null) {
        $ligne = ligne("SELECT {$r['champs_select']} {$r['depuis']} WHERE t.id = ?", [$id]);
        if (!$ligne) {
            page_erreur(404);
        }
    } elseif (!$r['peut_ajouter'] || !$peut_modifier) {
        page_erreur(403, 'Accès réservé', 'Cette rubrique ne permet pas d\'ajouter un élément.');
    }
    // Champs en lecture seule selon la fiche (ex. texte d'un témoignage envoyé par un membre)
    foreach ($r['groupes'] as &$g) {
        foreach ($g['champs'] as &$def) {
            if (($def['lecture_seule'] ?? null) instanceof Closure) {
                $def['lecture_seule'] = (bool) ($def['lecture_seule'])($ligne);
            }
        }
        unset($def);
    }
    unset($g);

    $groupes = groupes_de($r, $ligne);
    $champs = champs_de($groupes);
    $valeurs = [];
    foreach ($champs as $nom => $def) {
        if ($def['type'] === 'communautes') {
            $valeurs[$nom] = $ligne && $r['liaison'] ? array_map('intval', array_column(lignes("SELECT communaute_id FROM `{$r['liaison'][0]}` WHERE `{$r['liaison'][1]}` = ?", [$id]), 'communaute_id')) : [];
        } elseif ($ligne !== null && array_key_exists($nom, $ligne)) {
            $valeurs[$nom] = $ligne[$nom];
        } else {
            $defaut = $def['defaut'] ?? '';
            $valeurs[$nom] = $defaut === 'maintenant' ? maintenant() : $defaut;
        }
    }
    $erreurs = [];

    if (est_post()) {
        if (!$peut_modifier) {
            page_erreur(403);
        }
        [$donnees, $valeurs, $erreurs, $images] = lire_formulaire($champs, $valeurs, $ligne);
        if (!$erreurs && $r['avant']) {
            try {
                ($r['avant'])($donnees, $ligne);
            } catch (InvalidArgumentException $e) {
                [$champ, $message] = array_pad(explode('|', $e->getMessage(), 2), 2, '');
                $erreurs[$champ] = $message;
            }
        }
        if (!$erreurs) {
            // Images envoyées : enregistrées seulement quand tout le reste est valide
            foreach ($images as $nom => $def) {
                try {
                    $chemin = televerser_image($_FILES[$nom] ?? null, $def['dossier'] ?? $slug, $def['largeur_max'] ?? 1800);
                    if ($chemin) {
                        $donnees[$nom] = $chemin;
                    } elseif (post('supprimer_' . $nom) !== '') {
                        $donnees[$nom] = '';
                    }
                } catch (RuntimeException $e) {
                    $erreurs[$nom] = $e->getMessage();
                }
            }
        }
        if (!$erreurs) {
            $communautes_choisies = $donnees['__communautes'] ?? [];
            unset($donnees['__communautes']);
            bdd()->beginTransaction();
            try {
                if ($ligne) {
                    mettre_a_jour($table, $donnees, (int) $ligne['id']);
                    $id = (int) $ligne['id'];
                } else {
                    $id = inserer($table, $donnees);
                }
                if ($r['liaison']) {
                    [$t_liaison, $col] = $r['liaison'];
                    requete("DELETE FROM `$t_liaison` WHERE `$col` = ?", [$id]);
                    foreach ($communautes_choisies as $cid) {
                        inserer($t_liaison, [$col => $id, 'communaute_id' => $cid]);
                    }
                }
                if ($r['galerie']) {
                    enregistrer_galerie($r['galerie'], $id);
                }
                bdd()->commit();
            } catch (Throwable $e) {
                bdd()->rollBack();
                throw $e;
            }
            // Anciennes images remplacées : fichiers supprimés
            foreach ($images as $nom => $def) {
                if ($ligne && isset($donnees[$nom]) && $ligne[$nom] && $donnees[$nom] !== $ligne[$nom]) {
                    supprimer_image($ligne[$nom]);
                }
            }
            flash('succes', $ligne ? 'Modifications enregistrées.' : 'Élément ajouté.');
            if ($r['fiche_unique'] || isset($_POST['_continuer'])) {
                rediriger(url('/gestion/' . $slug . '/' . $id));
            }
            if (isset($_POST['_autre'])) {
                rediriger(url('/gestion/' . $slug . '/ajouter'));
            }
            rediriger(url('/gestion/' . $slug));
        }
        flash('erreur', 'Certaines informations sont à corriger : les champs concernés sont indiqués en rouge.');
    }

    $titre = $r['fiche_unique'] ? $r['titre'] : ($ligne ? couper(($r['nom_element'])($ligne), 70) : $r['titre_ajout']);
    $donnees_vue = [
        'titre_page' => $titre,
        'ariane' => array_merge($r['parent'] ? [$r['parent']] : [], $r['fiche_unique'] ? [[$r['titre'], null]] : [[$r['titre'], url('/gestion/' . $slug)], [$ligne ? couper(($r['nom_element'])($ligne), 40) : 'Ajouter', null]]),
        'r' => $r,
        'ligne' => $ligne,
        'groupes' => $groupes,
        'valeurs' => $valeurs,
        'erreurs' => $erreurs,
        'peut_modifier' => $peut_modifier,
        'galerie' => ($r['galerie'] && $ligne) ? lignes("SELECT * FROM `{$r['galerie'][0]}` WHERE `{$r['galerie'][1]}` = ? ORDER BY ordre, id", [$ligne['id']]) : [],
        'url_voir' => ($ligne && $r['url_voir'] && (!isset($ligne['statut']) || $ligne['statut'] === 'publie')) ? ($r['url_voir'])($ligne) : null,
    ];
    if ($slug === 'parametres') {
        $donnees_vue['equipe'] = lignes("SELECT * FROM utilisateurs WHERE type_compte = 'administration' AND actif = 1 ORDER BY role_admin = 'super' DESC, email");
        require_once RACINE . '/app/exemples.php';
        $donnees_vue['nb_exemples'] = nombre_exemples();
    }
    vue_admin('admin/formulaire', $donnees_vue);
}

/** Lit et vérifie les champs envoyés. Renvoie [données à enregistrer, valeurs à réafficher, erreurs, champs image]. */
function lire_formulaire(array $champs, array $valeurs, ?array $ligne): array
{
    $donnees = [];
    $erreurs = [];
    $images = [];
    foreach ($champs as $nom => $def) {
        $type = $def['type'];
        if (in_array($type, ['lecture', 'motdepasse'], true) || !empty($def['lecture_seule']) || !empty($def['virtuel'])) {
            if ($type === 'motdepasse' && $def['requis'] && (string) ($_POST[$nom] ?? '') === '') {
                $erreurs[$nom] = 'Ce champ est obligatoire.';
            }
            continue;
        }
        if ($type === 'image') {
            $images[$nom] = $def;
            continue;
        }
        if ($type === 'communautes') {
            $ids = array_values(array_unique(array_map('intval', (array) ($_POST[$nom] ?? []))));
            $valides = array_map('intval', array_column(lignes('SELECT id FROM communautes'), 'id'));
            $donnees['__communautes'] = array_values(array_intersect($ids, $valides));
            $valeurs[$nom] = $donnees['__communautes'];
            continue;
        }
        if ($type === 'case') {
            $valeurs[$nom] = isset($_POST[$nom]) ? 1 : 0;
            $donnees[$nom] = $valeurs[$nom];
            continue;
        }
        if ($type === 'cache') {
            $donnees[$nom] = $def['defaut'] ?? '';
            continue;
        }
        $v = post($nom);
        $valeurs[$nom] = $v;
        if ($v === '') {
            if ($def['requis']) {
                $erreurs[$nom] = 'Ce champ est obligatoire.';
            }
            $donnees[$nom] = in_array($type, ['date', 'datetime'], true) || !empty($def['nul']) ? null : ($type === 'nombre' ? 0 : '');
            continue;
        }
        if ($def['max'] && mb_strlen($v) > $def['max']) {
            $erreurs[$nom] = $def['max'] . ' caractères au maximum (actuellement ' . mb_strlen($v) . ').';
        }
        switch ($type) {
            case 'email':
                $v = mb_strtolower($v);
                if (!filter_var($v, FILTER_VALIDATE_EMAIL)) {
                    $erreurs[$nom] = "Cette adresse e-mail n'est pas valide.";
                }
                break;
            case 'url':
                if (!preg_match('#^https?://#i', $v)) {
                    $v = 'https://' . $v;
                }
                if (!filter_var($v, FILTER_VALIDATE_URL) || mb_strlen($v) > 255) {
                    $erreurs[$nom] = "Cette adresse web n'est pas valide.";
                }
                break;
            case 'nombre':
                if (!preg_match('/^\d+$/', $v)) {
                    $erreurs[$nom] = 'Indiquez un nombre entier.';
                }
                $v = (int) $v;
                break;
            case 'date':
                $d = DateTime::createFromFormat('Y-m-d', $v);
                if (!$d || $d->format('Y-m-d') !== $v) {
                    $erreurs[$nom] = 'Date non valide.';
                }
                break;
            case 'datetime':
                $d = DateTime::createFromFormat('Y-m-d\TH:i', $v) ?: DateTime::createFromFormat('Y-m-d H:i:s', $v);
                if (!$d) {
                    $erreurs[$nom] = 'Date non valide.';
                } else {
                    $v = $d->format('Y-m-d H:i:s');
                }
                break;
            case 'select':
                if (!array_key_exists($v, $def['options'])) {
                    $erreurs[$nom] = 'Choisissez une valeur dans la liste.';
                }
                break;
            case 'slug':
                $v = slugifier($v);
                break;
        }
        $donnees[$nom] = $v;
    }
    return [$donnees, $valeurs, $erreurs, $images];
}

/** Galerie de photos : légendes, ordre, suppressions et nouvelles photos. */
function enregistrer_galerie(array $galerie, int $id): void
{
    [$table, $col, $dossier] = $galerie;
    foreach (lignes("SELECT * FROM `$table` WHERE `$col` = ?", [$id]) as $photo) {
        $pid = (int) $photo['id'];
        if (isset($_POST['galerie_supprimer'][$pid])) {
            requete("DELETE FROM `$table` WHERE id = ?", [$pid]);
            supprimer_image($photo['image']);
            continue;
        }
        requete("UPDATE `$table` SET legende = ?, ordre = ? WHERE id = ?", [
            mb_substr(trim((string) ($_POST['galerie_legende'][$pid] ?? '')), 0, 200),
            max(0, (int) ($_POST['galerie_ordre'][$pid] ?? 0)),
            $pid,
        ]);
    }
    $fichiers = $_FILES['galerie_nouvelles'] ?? null;
    if ($fichiers && is_array($fichiers['name'])) {
        $ordre = (int) valeur("SELECT COALESCE(MAX(ordre), -1) + 1 FROM `$table` WHERE `$col` = ?", [$id]);
        foreach (array_keys($fichiers['name']) as $i) {
            $f = ['name' => $fichiers['name'][$i], 'type' => $fichiers['type'][$i], 'tmp_name' => $fichiers['tmp_name'][$i], 'error' => $fichiers['error'][$i], 'size' => $fichiers['size'][$i]];
            try {
                $chemin = televerser_image($f, $dossier, 1600);
                if ($chemin) {
                    inserer($table, [$col => $id, 'image' => $chemin, 'legende' => '', 'ordre' => $ordre++]);
                }
            } catch (RuntimeException $e) {
                flash('erreur', $f['name'] . ' : ' . $e->getMessage());
            }
        }
    }
}

function supprimer(string $slug, int $id): void
{
    $r = ressource_ou_404($slug);
    exiger_droit($r['rubrique'], 'modifier');
    if (!$r['peut_supprimer'] || $r['fiche_unique']) {
        page_erreur(403);
    }
    $ligne = ligne("SELECT * FROM `{$r['table']}` WHERE id = ?", [$id]);
    if (!$ligne) {
        page_erreur(404);
    }
    if (!empty($r['avant_suppression']) && ($refus = ($r['avant_suppression'])($ligne))) {
        flash('erreur', $refus);
        rediriger(url('/gestion/' . $slug));
    }
    if ($slug === 'comptes') {
        if ($id === (int) utilisateur()['id']) {
            flash('erreur', 'Vous ne pouvez pas supprimer votre propre compte depuis l\'administration.');
            rediriger(url('/gestion/comptes/' . $id));
        }
        if ($ligne['role_admin'] === 'super' && (int) valeur("SELECT COUNT(*) FROM utilisateurs WHERE role_admin = 'super'") <= 1) {
            flash('erreur', 'Il doit rester au moins un administrateur principal.');
            rediriger(url('/gestion/comptes/' . $id));
        }
        $profil = profil_de($ligne);
        supprimer_image($profil['photo'] ?? $profil['logo'] ?? null);
    }
    $photos = $r['galerie'] ? lignes("SELECT image FROM `{$r['galerie'][0]}` WHERE `{$r['galerie'][1]}` = ?", [$id]) : [];
    requete("DELETE FROM `{$r['table']}` WHERE id = ?", [$id]);
    foreach (champs_de(groupes_de($r, $ligne)) as $nom => $def) {
        if ($def['type'] === 'image' && !empty($ligne[$nom])) {
            supprimer_image($ligne[$nom]);
        }
    }
    foreach ($photos as $p) {
        supprimer_image($p['image']);
    }
    flash('succes', 'Élément supprimé.');
    rediriger(url('/gestion/' . $slug));
}

// ---------------------------------------------------------------------------
// Mon compte (compte d'administration) : nom affiché et mot de passe
// ---------------------------------------------------------------------------

function mon_compte(): void
{
    $u = exiger_admin();
    $valeurs = ['prenom_admin' => (string) $u['prenom_admin'], 'nom_admin' => (string) $u['nom_admin']];
    $erreurs = [];
    if (est_post()) {
        $action = post('action');
        if ($action === 'identite') {
            $valeurs = ['prenom_admin' => post('prenom_admin'), 'nom_admin' => post('nom_admin')];
            foreach ($valeurs as $k => $v) {
                if (mb_strlen($v) > 80) {
                    $erreurs[$k] = '80 caractères au maximum.';
                }
            }
            if (!$erreurs) {
                mettre_a_jour('utilisateurs', $valeurs, (int) $u['id']);
                flash('succes', 'Vos informations ont bien été enregistrées.');
                rediriger(url('/gestion/mon-compte'));
            }
        } elseif ($action === 'mdp') {
            $mdp1 = (string) ($_POST['password1'] ?? '');
            if (!password_verify((string) ($_POST['mot_de_passe_actuel'] ?? ''), $u['mot_de_passe'])) {
                $erreurs['mot_de_passe_actuel'] = 'Mot de passe actuel incorrect.';
            } elseif (($msg = verifier_mot_de_passe($mdp1, $u['email'])) !== '') {
                $erreurs['password1'] = $msg;
            } elseif ($mdp1 !== (string) ($_POST['password2'] ?? '')) {
                $erreurs['password2'] = 'Les deux mots de passe ne correspondent pas.';
            } else {
                $hash = password_hash($mdp1, PASSWORD_DEFAULT);
                requete('UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?', [$hash, $u['id']]);
                $u['mot_de_passe'] = $hash;
                connecter($u);
                flash('succes', 'Votre mot de passe a bien été modifié.');
                rediriger(url('/gestion/mon-compte'));
            }
        }
    }
    vue_admin('admin/mon_compte', [
        'titre_page' => 'Mon compte',
        'ariane' => [['Mon compte', null]],
        'moi' => $u,
        'valeurs' => $valeurs,
        'erreurs' => $erreurs,
    ]);
}
