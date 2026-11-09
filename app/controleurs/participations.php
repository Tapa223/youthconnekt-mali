<?php
/*
 * Participations aux initiatives.
 * Administration : l'équipe ouvre les participations, crée les rôles et les questions, puis traite les candidatures.
 * Site : le membre choisit un rôle et répond aux questions ; il suit sa demande dans son espace.
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// Administration
// ---------------------------------------------------------------------------

/** Rôles, questions et réglages des participations d'une initiative, plus la liste des candidatures. */
function participations_configurer(int $id): void
{
    require_once RACINE . '/app/controleurs/admin.php';
    exiger_droit('initiatives');
    $i = ligne('SELECT * FROM initiatives WHERE id = ?', [$id]);
    if (!$i) {
        page_erreur(404);
    }
    $peut_modifier = peut('initiatives', 'modifier');
    $erreurs = [];

    if (est_post()) {
        if (!$peut_modifier) {
            page_erreur(403);
        }
        $fin = post('participations_fin');
        if ($fin !== '' && !DateTimeImmutable::createFromFormat('!Y-m-d', $fin)) {
            $erreurs['participations_fin'] = "Cette date n'est pas valide.";
        }
        // Rôles : une ligne par rôle (identifiant gardé pour ne pas perdre les candidatures déjà reçues)
        $roles = [];
        foreach ((array) ($_POST['role_nom'] ?? []) as $k => $nom) {
            $nom = trim((string) $nom);
            if ($nom === '') {
                continue;
            }
            $places = trim((string) ($_POST['role_places'][$k] ?? ''));
            $roles[] = [
                'id' => (int) ($_POST['role_id'][$k] ?? 0),
                'nom' => mb_substr($nom, 0, 120),
                'description' => mb_substr(trim((string) ($_POST['role_description'][$k] ?? '')), 0, 300),
                'places' => $places === '' ? null : max(1, (int) $places),
            ];
        }
        // Questions du formulaire
        $champs = [];
        foreach ((array) ($_POST['champ_libelle'] ?? []) as $k => $libelle) {
            $libelle = trim((string) $libelle);
            if ($libelle === '') {
                continue;
            }
            $type = (string) ($_POST['champ_type'][$k] ?? 'texte');
            $type = isset(TYPES_CHAMP_PARTICIPATION[$type]) ? $type : 'texte';
            $options = implode("\n", array_slice(lignes_texte((string) ($_POST['champ_options'][$k] ?? '')), 0, 40));
            if (in_array($type, ['choix', 'cases'], true) && $options === '') {
                $erreurs['champs'] = "La question « $libelle » propose des choix : indiquez-les, un par ligne.";
            }
            $champs[] = [
                'id' => (int) ($_POST['champ_id'][$k] ?? 0),
                'libelle' => mb_substr($libelle, 0, 200), 'type' => $type,
                'options' => in_array($type, ['choix', 'cases'], true) ? $options : '',
                'aide' => mb_substr(trim((string) ($_POST['champ_aide'][$k] ?? '')), 0, 300),
                'requis' => !empty($_POST['champ_requis'][$k]) ? 1 : 0,
            ];
        }
        $ouvertes = post('participations_ouvertes') !== '' ? 1 : 0;
        if ($ouvertes && !$roles) {
            $erreurs['roles'] = 'Ajoutez au moins un rôle (par exemple « Bénévole ») pour ouvrir les participations.';
        }
        if (!$erreurs) {
            bdd()->beginTransaction();
            try {
                mettre_a_jour('initiatives', [
                    'participations_ouvertes' => $ouvertes,
                    'participations_fin' => $fin !== '' ? $fin : null,
                    'participations_intro' => mb_substr(post('participations_intro'), 0, 1000),
                    'modifie_le' => maintenant(),
                ], $id);
                synchroniser_lignes('initiative_roles', $id, $roles, ['nom', 'description', 'places']);
                synchroniser_lignes('initiative_champs', $id, $champs, ['libelle', 'type', 'options', 'aide', 'requis']);
                bdd()->commit();
            } catch (Throwable $e) {
                bdd()->rollBack();
                throw $e;
            }
            flash('succes', $ouvertes ? 'Participations enregistrées et ouvertes : le formulaire est visible sur la page de l\'initiative.' : 'Participations enregistrées. Elles restent fermées tant que la case « Ouvrir » n\'est pas cochée.');
            rediriger(url('/gestion/initiatives/' . $id . '/participations'));
        }
        flash('erreur', 'Certaines informations sont à corriger.');
        $i = array_merge($i, ['participations_ouvertes' => $ouvertes, 'participations_fin' => $fin, 'participations_intro' => post('participations_intro')]);
        $roles_vue = $roles;
        $champs_vue = $champs;
    }

    $statut = isset(STATUTS_PARTICIPATION[get('statut')]) ? get('statut') : '';
    $role_filtre = (int) get('role');
    $conditions = ['p.initiative_id = ?'];
    $params = [$id];
    if ($statut) {
        $conditions[] = 'p.statut = ?';
        $params[] = $statut;
    }
    if ($role_filtre) {
        $conditions[] = 'p.role_id = ?';
        $params[] = $role_filtre;
    }
    vue_admin('admin/participations', [
        'titre_page' => 'Participations · ' . $i['titre'],
        'ariane' => [['Initiatives', url('/gestion/initiatives')], [couper($i['titre'], 40), url('/gestion/initiatives/' . $id)], ['Participations', null]],
        'i' => $i,
        'roles' => $roles_vue ?? roles_initiative($id),
        'champs' => $champs_vue ?? champs_initiative($id),
        'erreurs' => $erreurs,
        'peut_modifier' => $peut_modifier,
        'onglet' => get('vue') === 'formulaire' || $erreurs || !$i['participations_ouvertes'] && !valeur('SELECT COUNT(*) FROM participations WHERE initiative_id = ?', [$id]) ? 'formulaire' : 'candidatures',
        'candidatures' => lignes(
            "SELECT p.*, COALESCE(r.nom, p.role_nom) AS role_libelle, u.email,
                    COALESCE(CONCAT(pi.prenom, ' ', pi.nom), po.nom_structure, u.email) AS membre_nom, pi.genre, pi.date_naissance, pi.region AS region_i, pi.telephone
             FROM participations p JOIN utilisateurs u ON u.id = p.utilisateur_id
             LEFT JOIN profils_individuels pi ON pi.utilisateur_id = u.id LEFT JOIN profils_organisations po ON po.utilisateur_id = u.id
             LEFT JOIN initiative_roles r ON r.id = p.role_id
             WHERE " . implode(' AND ', $conditions) . " ORDER BY p.statut = 'en_attente' DESC, p.cree_le DESC",
            $params
        ),
        'compteurs' => array_column(lignes('SELECT statut, COUNT(*) AS n FROM participations WHERE initiative_id = ? GROUP BY statut', [$id]), 'n', 'statut'),
        'statut' => $statut,
        'role_filtre' => $role_filtre,
    ]);
}

/**
 * Met à jour une liste de lignes rattachées à une initiative (rôles ou questions) :
 * les lignes gardées sont modifiées, les nouvelles ajoutées, les retirées supprimées.
 */
function synchroniser_lignes(string $table, int $initiative_id, array $lignes_voulues, array $colonnes): void
{
    $existantes = array_map('intval', array_column(lignes("SELECT id FROM `$table` WHERE initiative_id = ?", [$initiative_id]), 'id'));
    $gardees = [];
    foreach ($lignes_voulues as $ordre => $l) {
        $donnees = array_intersect_key($l, array_flip($colonnes)) + ['ordre' => $ordre];
        if ($l['id'] && in_array($l['id'], $existantes, true)) {
            mettre_a_jour($table, $donnees, $l['id']);
            $gardees[] = $l['id'];
        } else {
            inserer($table, $donnees + ['initiative_id' => $initiative_id]);
        }
    }
    $retirees = array_diff($existantes, $gardees);
    if ($retirees) {
        if ($table === 'initiative_roles') {
            // Le nom du rôle reste sur les candidatures déjà reçues
            requete('UPDATE participations p JOIN initiative_roles r ON r.id = p.role_id SET p.role_nom = r.nom WHERE r.id IN (' . marqueurs(array_values($retirees)) . ')', array_values($retirees));
        }
        requete("DELETE FROM `$table` WHERE id IN (" . marqueurs(array_values($retirees)) . ')', array_values($retirees));
    }
}

/** Décision sur une candidature (depuis sa fiche ou la liste de l'initiative). */
function participation_decision(int $id): void
{
    require_once RACINE . '/app/controleurs/admin.php';
    exiger_droit('initiatives', 'modifier');
    $p = ligne('SELECT * FROM participations WHERE id = ?', [$id]);
    if (!$p) {
        page_erreur(404);
    }
    $statut = post('statut');
    if (!isset(STATUTS_PARTICIPATION[$statut])) {
        page_erreur(400);
    }
    mettre_a_jour('participations', ['statut' => $statut, 'traitee_le' => $statut === 'en_attente' ? null : maintenant()], $id);
    flash('succes', ['retenue' => 'Candidature retenue : la personne en est prévenue dans son espace.', 'non_retenue' => 'Candidature non retenue : la personne en est prévenue dans son espace.', 'en_attente' => 'Candidature remise en attente.'][$statut]);
    rediriger(adresse_retour(url('/gestion/initiatives/' . $p['initiative_id'] . '/participations')));
}

/** Tableau des candidatures d'une initiative, une colonne par question (à ouvrir dans un tableur). */
function participations_exporter(int $id): void
{
    require_once RACINE . '/app/controleurs/admin.php';
    exiger_droit('initiatives');
    $i = ligne('SELECT titre, slug FROM initiatives WHERE id = ?', [$id]);
    if (!$i) {
        page_erreur(404);
    }
    $champs = champs_initiative($id);
    $lignes = lignes(
        "SELECT p.*, COALESCE(r.nom, p.role_nom) AS role_libelle, u.email,
                COALESCE(CONCAT(pi.prenom, ' ', pi.nom), po.nom_structure, u.email) AS membre_nom, pi.genre, pi.date_naissance, pi.region AS region_i, pi.telephone
         FROM participations p JOIN utilisateurs u ON u.id = p.utilisateur_id
         LEFT JOIN profils_individuels pi ON pi.utilisateur_id = u.id LEFT JOIN profils_organisations po ON po.utilisateur_id = u.id
         LEFT JOIN initiative_roles r ON r.id = p.role_id WHERE p.initiative_id = ? ORDER BY role_libelle, p.cree_le",
        [$id]
    );
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="participations-' . $i['slug'] . '.csv"');
    $sortie = fopen('php://output', 'w');
    fwrite($sortie, "\xEF\xBB\xBF");
    fputcsv($sortie, array_merge(['Rôle', 'Nom', 'E-mail', 'Téléphone', 'Genre', 'Âge', 'Région', 'Décision', 'Reçue le'], array_column($champs, 'libelle')), ';');
    foreach ($lignes as $l) {
        $rep = array_column(reponses_participation($l), 'reponse', 'id');
        fputcsv($sortie, array_merge([
            $l['role_libelle'], $l['membre_nom'], $l['email'], $l['telephone'], GENRES[$l['genre'] ?? ''] ?? '', age($l['date_naissance'] ?? null) ?? '',
            REGIONS[$l['region_i'] ?? ''] ?? '', STATUTS_PARTICIPATION[$l['statut']] ?? '', date_chiffres($l['cree_le']),
        ], array_map(fn ($c) => $rep[$c['id']] ?? '', $champs)), ';');
    }
    exit;
}

// ---------------------------------------------------------------------------
// Site et espace membre
// ---------------------------------------------------------------------------

/** Formulaire « Prendre part à cette initiative » (connexion demandée). */
function participer(string $slug): void
{
    $i = initiatives_publiees('AND i.slug = ? LIMIT 1', [$slug])[0] ?? null;
    if (!$i) {
        page_erreur(404);
    }
    $ici = url('/initiatives/' . $slug . '/participer', ['role' => get('role') ?: null]);
    $u = utilisateur();
    if (!$u) {
        flash('info', 'Connectez-vous pour prendre part à cette initiative : vos informations seront reprises de votre compte.');
        rediriger(url('/compte/connexion', ['suivant' => $ici]));
    }
    if (!est_membre($u)) {
        flash('erreur', "Les participations sont réservées aux comptes de membres.");
        rediriger(url('/initiatives/' . $slug));
    }
    $deja = ligne('SELECT p.*, COALESCE(r.nom, p.role_nom) AS role_libelle FROM participations p LEFT JOIN initiative_roles r ON r.id = p.role_id WHERE p.initiative_id = ? AND p.utilisateur_id = ?', [$i['id'], $u['id']]);
    $roles = roles_initiative((int) $i['id']);
    $champs = champs_initiative((int) $i['id']);
    $ouvertes = participations_ouvertes($i, $roles);
    $erreurs = [];
    $valeurs = ['role' => (string) (count($roles) === 1 ? $roles[0]['id'] : get('role'))];

    if (est_post() && !$deja && $ouvertes) {
        $valeurs['role'] = post('role');
        $role = null;
        foreach ($roles as $r) {
            if ((string) $r['id'] === $valeurs['role'] && !$r['complet']) {
                $role = $r;
            }
        }
        if (!$role) {
            $erreurs['role'] = 'Choisissez comment vous souhaitez participer.';
        }
        [$reponses, $erreurs_champs] = lire_reponses($champs);
        $erreurs += $erreurs_champs;
        foreach ($reponses as $rep) {
            $valeurs['champ_' . $rep['id']] = $rep['brut'];
        }
        if (!$erreurs) {
            inserer('participations', [
                'initiative_id' => $i['id'], 'role_id' => $role['id'], 'role_nom' => $role['nom'], 'utilisateur_id' => $u['id'],
                'reponses' => json_encode(array_map(fn ($r) => ['id' => $r['id'], 'question' => $r['question'], 'reponse' => $r['reponse']], $reponses), JSON_UNESCAPED_UNICODE),
                'statut' => 'en_attente', 'cree_le' => maintenant(),
            ]);
            flash('succes', "Merci ! Votre candidature comme « " . $role['nom'] . " » a bien été envoyée. L'équipe vous répond dans votre espace.");
            rediriger(url('/compte/mon-espace/participations'));
        }
    }

    afficher(vue('pages/participer', [
        'titre' => 'Prendre part : ' . $i['titre'] . ' | YouthConnekt Mali',
        'description' => $i['resume'],
        'i' => $i, 'u' => $u, 'roles' => $roles, 'champs' => $champs, 'ouvertes' => $ouvertes,
        'deja' => $deja, 'erreurs' => $erreurs, 'valeurs' => $valeurs,
    ]));
}

/** Espace membre : mes candidatures aux initiatives. */
function mes_participations(): void
{
    require_once RACINE . '/app/controleurs/espace.php';
    $u = exiger_membre();
    vue_espace('participations', 'participations', 'Mes participations', [
        'participations' => lignes(
            "SELECT p.*, COALESCE(r.nom, p.role_nom) AS role_libelle, i.titre, i.slug, i.date_libelle, i.lieu
             FROM participations p JOIN initiatives i ON i.id = p.initiative_id LEFT JOIN initiative_roles r ON r.id = p.role_id
             WHERE p.utilisateur_id = ? ORDER BY p.cree_le DESC",
            [$u['id']]
        ),
    ], $u);
}
