<?php
/*
 * Participations aux initiatives.
 * Administration : l'équipe ouvre les participations, crée les rôles (inscription ou candidature)
 * et les questions de chaque rôle, trie et retient les candidatures, pointe les présents.
 * Site : le membre s'inscrit ou se propose ; il retrouve sa demande et son billet dans son espace.
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// Administration
// ---------------------------------------------------------------------------

/** Rôles et questions, ouverture, liste des inscrits et des candidatures. */
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
        // Rôles, chacun avec ses questions (les identifiants sont gardés pour ne pas perdre les candidatures reçues)
        $roles = [];
        foreach ((array) ($_POST['role_nom'] ?? []) as $rk => $nom) {
            $nom = trim((string) $nom);
            if ($nom === '') {
                continue;
            }
            $places = trim((string) ($_POST['role_places'][$rk] ?? ''));
            $mode = (string) ($_POST['role_mode'][$rk] ?? 'candidature');
            $champs = [];
            foreach ((array) ($_POST['champ_libelle'][$rk] ?? []) as $ck => $libelle) {
                $libelle = trim((string) $libelle);
                if ($libelle === '') {
                    continue;
                }
                $type = (string) ($_POST['champ_type'][$rk][$ck] ?? 'texte');
                $type = isset(TYPES_CHAMP_PARTICIPATION[$type]) ? $type : 'texte';
                $options = implode("\n", array_slice(lignes_texte((string) ($_POST['champ_options'][$rk][$ck] ?? '')), 0, 40));
                if (in_array($type, ['choix', 'cases'], true) && $options === '') {
                    $erreurs['roles'] = "La question « $libelle » propose des choix : indiquez-les, un par ligne.";
                }
                $champs[] = [
                    'id' => (int) ($_POST['champ_id'][$rk][$ck] ?? 0),
                    'libelle' => mb_substr($libelle, 0, 200), 'type' => $type,
                    'options' => in_array($type, ['choix', 'cases'], true) ? $options : '',
                    'aide' => mb_substr(trim((string) ($_POST['champ_aide'][$rk][$ck] ?? '')), 0, 300),
                    'requis' => !empty($_POST['champ_requis'][$rk][$ck]) ? 1 : 0,
                ];
            }
            $roles[] = [
                'id' => (int) ($_POST['role_id'][$rk] ?? 0),
                'nom' => mb_substr($nom, 0, 120),
                'mode' => isset(MODES_ROLE[$mode]) ? $mode : 'candidature',
                'description' => mb_substr(trim((string) ($_POST['role_description'][$rk] ?? '')), 0, 300),
                'places' => $places === '' ? null : max(1, (int) $places),
                'champs' => $champs,
            ];
        }
        $ouvertes = post('participations_ouvertes') !== '' ? 1 : 0;
        if ($ouvertes && !$roles) {
            $erreurs['roles'] = 'Ajoutez au moins un rôle (par exemple « Participant » ou « Bénévole ») pour ouvrir les participations.';
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
                enregistrer_roles($id, $roles);
                bdd()->commit();
            } catch (Throwable $e) {
                bdd()->rollBack();
                throw $e;
            }
            flash('succes', $ouvertes ? "Participations enregistrées et ouvertes : elles sont visibles sur la page de l'initiative." : "Participations enregistrées. Elles restent fermées tant que la case « Ouvrir » n'est pas cochée.");
            rediriger(url('/gestion/initiatives/' . $id . '/participations', ['vue' => 'formulaire']));
        }
        flash('erreur', 'Certaines informations sont à corriger.');
        $i = array_merge($i, ['participations_ouvertes' => $ouvertes, 'participations_fin' => $fin, 'participations_intro' => post('participations_intro')]);
        $roles_vue = $roles;
    }

    $roles = roles_initiative($id);
    $total = (int) valeur('SELECT COUNT(*) FROM participations WHERE initiative_id = ?', [$id]);
    $onglet = get('vue') === 'formulaire' || $erreurs || (!$total && !$i['participations_ouvertes']) ? 'formulaire' : 'liste';

    // Liste : filtres (rôle, décision, région, âge, présence, réponse à une question) et tri
    $f = [
        'statut' => isset(STATUTS_PARTICIPATION[get('statut')]) ? get('statut') : '',
        'role' => (int) get('role'),
        'region' => isset(REGIONS[get('region')]) ? get('region') : '',
        'age' => isset(TRANCHES_AGE[get('age')]) ? get('age') : '',
        'presence' => in_array(get('presence'), ['oui', 'non'], true) ? get('presence') : '',
        'tri' => in_array(get('tri'), ['recentes', 'anciennes', 'nom', 'age'], true) ? get('tri') : 'recentes',
        'affichage' => get('affichage') === 'tableau' ? 'tableau' : 'cartes',
        'q' => array_filter(array_map('strval', (array) ($_GET['q'] ?? [])), fn ($v) => $v !== ''),
    ];
    $conditions = ['p.initiative_id = ?'];
    $params = [$id];
    foreach (['statut' => 'p.statut', 'role' => 'p.role_id', 'region' => 'pi.region'] as $cle => $colonne) {
        if ($f[$cle]) {
            $conditions[] = "$colonne = ?";
            $params[] = $f[$cle];
        }
    }
    if ($f['age']) {
        [, $min, $max] = TRANCHES_AGE[$f['age']];
        $conditions[] = 'TIMESTAMPDIFF(YEAR, pi.date_naissance, CURDATE()) BETWEEN ? AND ?';
        array_push($params, $min, $max);
    }
    if ($f['presence']) {
        $conditions[] = $f['presence'] === 'oui' ? 'p.present_le IS NOT NULL' : 'p.present_le IS NULL';
    }
    $ordre = ['recentes' => 'p.cree_le DESC', 'anciennes' => 'p.cree_le', 'nom' => 'membre_nom', 'age' => 'pi.date_naissance DESC'][$f['tri']];
    $liste = lignes(
        "SELECT p.*, COALESCE(r.nom, p.role_nom) AS role_libelle, r.mode AS role_mode, u.email,
                COALESCE(CONCAT(pi.prenom, ' ', pi.nom), po.nom_structure, u.email) AS membre_nom, pi.genre, pi.date_naissance, pi.region AS region_i, pi.telephone
         FROM participations p JOIN utilisateurs u ON u.id = p.utilisateur_id
         LEFT JOIN profils_individuels pi ON pi.utilisateur_id = u.id LEFT JOIN profils_organisations po ON po.utilisateur_id = u.id
         LEFT JOIN initiative_roles r ON r.id = p.role_id
         WHERE " . implode(' AND ', $conditions) . " ORDER BY p.statut = 'en_attente' DESC, $ordre",
        $params
    );
    // Filtre sur la réponse à une question (choix, oui/non) : appliqué sur les réponses enregistrées
    if ($f['q']) {
        $liste = array_values(array_filter($liste, function ($p) use ($f) {
            $rep = array_column(reponses_participation($p), 'reponse', 'id');
            foreach ($f['q'] as $champ_id => $voulu) {
                if (!in_array($voulu, array_map('trim', explode(',', (string) ($rep[(int) $champ_id] ?? ''))), true)) {
                    return false;
                }
            }
            return true;
        }));
    }
    $role_choisi = null;
    foreach ($roles as $r) {
        if ((int) $r['id'] === $f['role']) {
            $role_choisi = $r;
        }
    }

    vue_admin('admin/participations', [
        'titre_page' => 'Participations · ' . $i['titre'],
        'ariane' => [['Initiatives', url('/gestion/initiatives')], [couper($i['titre'], 40), url('/gestion/initiatives/' . $id)], ['Participations', null]],
        'i' => $i,
        'roles' => $roles,
        'roles_form' => $roles_vue ?? $roles,
        'erreurs' => $erreurs,
        'peut_modifier' => $peut_modifier,
        'onglet' => $onglet,
        'liste' => $liste,
        'total' => $total,
        'compteurs' => array_column(lignes('SELECT statut, COUNT(*) AS n FROM participations WHERE initiative_id = ? GROUP BY statut', [$id]), 'n', 'statut'),
        'nb_presents' => (int) valeur('SELECT COUNT(*) FROM participations WHERE initiative_id = ? AND present_le IS NOT NULL', [$id]),
        'f' => $f,
        'role_choisi' => $role_choisi,
        // Questions affichées dans le tableau : celles du rôle filtré, sinon toutes
        'colonnes_questions' => $role_choisi ? $role_choisi['champs'] : champs_initiative($id),
    ]);
}

/** Enregistre les rôles et leurs questions : gardés, modifiés, ajoutés ou retirés. */
function enregistrer_roles(int $initiative_id, array $roles): void
{
    $existants = array_map('intval', array_column(lignes('SELECT id FROM initiative_roles WHERE initiative_id = ?', [$initiative_id]), 'id'));
    $gardes = [];
    foreach ($roles as $ordre => $r) {
        $donnees = ['nom' => $r['nom'], 'mode' => $r['mode'], 'description' => $r['description'], 'places' => $r['places'], 'ordre' => $ordre];
        if ($r['id'] && in_array($r['id'], $existants, true)) {
            mettre_a_jour('initiative_roles', $donnees, $r['id']);
            $role_id = $r['id'];
        } else {
            $role_id = inserer('initiative_roles', $donnees + ['initiative_id' => $initiative_id]);
        }
        $gardes[] = $role_id;

        $champs_existants = array_map('intval', array_column(lignes('SELECT id FROM initiative_champs WHERE role_id = ?', [$role_id]), 'id'));
        $champs_gardes = [];
        foreach ($r['champs'] as $o => $c) {
            $dc = ['libelle' => $c['libelle'], 'type' => $c['type'], 'options' => $c['options'], 'aide' => $c['aide'], 'requis' => $c['requis'], 'ordre' => $o];
            if ($c['id'] && in_array($c['id'], $champs_existants, true)) {
                mettre_a_jour('initiative_champs', $dc, $c['id']);
                $champs_gardes[] = $c['id'];
            } else {
                inserer('initiative_champs', $dc + ['initiative_id' => $initiative_id, 'role_id' => $role_id]);
            }
        }
        $retires = array_values(array_diff($champs_existants, $champs_gardes));
        if ($retires) {
            requete('DELETE FROM initiative_champs WHERE id IN (' . marqueurs($retires) . ')', $retires);
        }
    }
    $retires = array_values(array_diff($existants, $gardes));
    if ($retires) {
        // Le nom du rôle reste sur les participations déjà reçues
        requete('UPDATE participations p JOIN initiative_roles r ON r.id = p.role_id SET p.role_nom = r.nom WHERE r.id IN (' . marqueurs($retires) . ')', $retires);
        requete('DELETE FROM initiative_roles WHERE id IN (' . marqueurs($retires) . ')', $retires);
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
    if ($statut === 'present') {
        page_erreur(400);
    }
    mettre_a_jour('participations', ['statut' => $statut, 'traitee_le' => in_array($statut, ['en_attente', 'inscrit'], true) ? null : maintenant()], $id);
    if (in_array($statut, ['retenue', 'non_retenue'], true) && $statut !== $p['statut']) {
        prevenir_participant($id, $statut);
    }
    flash('succes', [
        'retenue' => 'Candidature retenue : la personne en est prévenue et reçoit son billet dans son espace.',
        'non_retenue' => 'Candidature non retenue : la personne en est prévenue dans son espace.',
        'en_attente' => 'Candidature remise en attente.',
        'inscrit' => 'Inscription rétablie.',
    ][$statut]);
    rediriger(adresse_retour(url('/gestion/initiatives/' . $p['initiative_id'] . '/participations')));
}

/** Pointage à l'entrée : par le code du billet (saisi ou scanné) ou depuis la liste. */
function participation_pointer(int $id): void
{
    require_once RACINE . '/app/controleurs/admin.php';
    exiger_droit('initiatives', 'modifier');
    $retour = adresse_retour(url('/gestion/initiatives/' . $id . '/participations'));
    $code = strtoupper(trim(post('code')));
    if ($code !== '' && !str_starts_with($code, 'YCM-')) {
        $code = 'YCM-' . $code;
    }
    $p = post('participation') !== ''
        ? ligne('SELECT * FROM participations WHERE id = ? AND initiative_id = ?', [(int) post('participation'), $id])
        : ligne('SELECT * FROM participations WHERE code = ? AND initiative_id = ?', [$code, $id]);
    if (!$p) {
        flash('erreur', "Aucun billet $code pour cette initiative.");
        rediriger($retour);
    }
    $nom = (string) valeur("SELECT COALESCE(CONCAT(pi.prenom, ' ', pi.nom), po.nom_structure, u.email) FROM utilisateurs u LEFT JOIN profils_individuels pi ON pi.utilisateur_id = u.id LEFT JOIN profils_organisations po ON po.utilisateur_id = u.id WHERE u.id = ?", [$p['utilisateur_id']]);
    if (post('annuler') !== '') {
        mettre_a_jour('participations', ['present_le' => null], (int) $p['id']);
        flash('succes', "Présence de $nom retirée.");
    } elseif (!billet_valable($p)) {
        flash('erreur', "Le billet de $nom n'est pas valable : sa candidature n'a pas été retenue.");
    } elseif ($p['present_le']) {
        flash('info', "$nom est déjà pointé présent (depuis " . date('H \h i', strtotime($p['present_le'])) . ').');
    } else {
        mettre_a_jour('participations', ['present_le' => maintenant()], (int) $p['id']);
        flash('succes', "$nom est pointé présent.");
    }
    rediriger($retour);
}

/** Tableau des participations d'une initiative, une colonne par question (à ouvrir dans un tableur). */
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
    fputcsv($sortie, array_merge(['Rôle', 'Nom', 'E-mail', 'Téléphone', 'Genre', 'Âge', 'Région', 'Décision', 'Billet', 'Présent', 'Reçue le'],
        array_map(fn ($c) => $c['libelle'] . ' (' . $c['role_nom'] . ')', $champs)), ';');
    foreach ($lignes as $l) {
        $rep = array_column(reponses_participation($l), 'reponse', 'id');
        fputcsv($sortie, array_merge([
            $l['role_libelle'], $l['membre_nom'], $l['email'], $l['telephone'], GENRES[$l['genre'] ?? ''] ?? '', age($l['date_naissance'] ?? null) ?? '',
            REGIONS[$l['region_i'] ?? ''] ?? '', STATUTS_PARTICIPATION[$l['statut']] ?? '', billet_valable($l) ? $l['code'] : '',
            $l['present_le'] ? 'oui' : '', date_chiffres($l['cree_le']),
        ], array_map(fn ($c) => $rep[$c['id']] ?? '', $champs)), ';');
    }
    exit;
}

// ---------------------------------------------------------------------------
// Site et espace membre
// ---------------------------------------------------------------------------

/** S'inscrire ou se proposer (connexion demandée). */
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
        flash('erreur', 'Les participations sont réservées aux comptes de membres.');
        rediriger(url('/initiatives/' . $slug));
    }
    $deja = ligne('SELECT p.*, COALESCE(r.nom, p.role_nom) AS role_libelle FROM participations p LEFT JOIN initiative_roles r ON r.id = p.role_id WHERE p.initiative_id = ? AND p.utilisateur_id = ?', [$i['id'], $u['id']]);
    $roles = roles_initiative((int) $i['id']);
    $ouvertes = participations_ouvertes($i, $roles);
    $erreurs = [];
    $libres = array_values(array_filter($roles, fn ($r) => !$r['complet']));
    $valeurs = ['role' => (string) (get('role') ?: (count($libres) === 1 ? $libres[0]['id'] : ''))];

    if (est_post() && !$deja && $ouvertes) {
        $valeurs['role'] = post('role');
        $role = null;
        foreach ($libres as $r) {
            if ((string) $r['id'] === $valeurs['role']) {
                $role = $r;
            }
        }
        $reponses = [];
        if (!$role) {
            $erreurs['role'] = 'Choisissez comment vous souhaitez participer.';
        } else {
            [$reponses, $erreurs_champs] = lire_reponses($role['champs']);
            $erreurs += $erreurs_champs;
            foreach ($reponses as $rep) {
                $valeurs['champ_' . $rep['id']] = $rep['brut'];
            }
        }
        if (!$erreurs) {
            $inscription = $role['mode'] === 'inscription';
            $pid = inserer('participations', [
                'initiative_id' => $i['id'], 'role_id' => $role['id'], 'role_nom' => $role['nom'], 'utilisateur_id' => $u['id'],
                'reponses' => json_encode(array_map(fn ($r) => ['id' => $r['id'], 'question' => $r['question'], 'reponse' => $r['reponse']], $reponses), JSON_UNESCAPED_UNICODE),
                'statut' => $inscription ? 'inscrit' : 'en_attente', 'code' => nouveau_code_billet(), 'cree_le' => maintenant(),
            ]);
            prevenir_participant($pid, $inscription ? 'inscrit' : 'recue');
            if ($inscription) {
                flash('succes', 'Vous êtes inscrit ! Voici votre billet : présentez-le à l\'entrée, sur votre téléphone ou imprimé.');
                rediriger(url('/compte/mon-espace/participations/' . $pid . '/billet'));
            }
            flash('succes', 'Merci ! Votre candidature comme « ' . $role['nom'] . " » a bien été envoyée. L'équipe vous répond dans votre espace.");
            rediriger(url('/compte/mon-espace/participations'));
        }
    }

    afficher(vue('pages/participer', [
        'titre' => 'Prendre part : ' . $i['titre'] . ' | YouthConnekt Mali',
        'description' => $i['resume'],
        'i' => $i, 'u' => $u, 'roles' => $roles, 'ouvertes' => $ouvertes,
        'deja' => $deja, 'erreurs' => $erreurs, 'valeurs' => $valeurs,
    ]));
}

/** Espace membre : mes inscriptions et candidatures aux initiatives. */
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

/** Billet virtuel : code et QR code à présenter à l'entrée. */
function mon_billet(int $id): void
{
    require_once RACINE . '/app/controleurs/espace.php';
    $u = exiger_membre();
    $p = ligne(
        "SELECT p.*, COALESCE(r.nom, p.role_nom) AS role_libelle, i.titre, i.slug, i.date_libelle, i.lieu, i.image, i.date_debut, i.etat
         FROM participations p JOIN initiatives i ON i.id = p.initiative_id LEFT JOIN initiative_roles r ON r.id = p.role_id
         WHERE p.id = ? AND p.utilisateur_id = ?",
        [$id, $u['id']]
    );
    if (!$p) {
        page_erreur(404);
    }
    if (!billet_valable($p)) {
        flash('info', "Le billet sera disponible dès que l'équipe aura retenu votre candidature.");
        rediriger(url('/compte/mon-espace/participations'));
    }
    if (!$p['code']) {
        $p['code'] = nouveau_code_billet();
        mettre_a_jour('participations', ['code' => $p['code']], $id);
    }
    vue_espace('billet', 'participations', 'Mon billet', ['p' => $p], $u);
}

/** E-mail à la personne : inscription (avec son billet), candidature reçue, retenue ou non retenue. */
function prevenir_participant(int $id, string $quoi): void
{
    $p = ligne(
        "SELECT p.*, u.email, COALESCE(pi.prenom, po.nom_structure, '') AS prenom, COALESCE(r.nom, p.role_nom) AS role_libelle, i.titre, i.slug, i.date_libelle, i.lieu
         FROM participations p JOIN utilisateurs u ON u.id = p.utilisateur_id JOIN initiatives i ON i.id = p.initiative_id
         LEFT JOIN profils_individuels pi ON pi.utilisateur_id = u.id LEFT JOIN profils_organisations po ON po.utilisateur_id = u.id
         LEFT JOIN initiative_roles r ON r.id = p.role_id WHERE p.id = ?",
        [$id]
    );
    if (!$p) {
        return;
    }
    $billet = url_absolue('/compte/mon-espace/participations/' . $id . '/billet');
    $quand = implode(' · ', array_filter([$p['date_libelle'], $p['lieu']]));
    $bonjour = 'Bonjour' . ($p['prenom'] !== '' ? ' ' . $p['prenom'] : '') . ",\n\n";
    [$sujet, $texte] = match ($quoi) {
        'inscrit' => ['Votre billet : ' . $p['titre'], $bonjour . "Vous êtes inscrit comme « {$p['role_libelle']} » à « {$p['titre']} »" . ($quand ? " ($quand)" : '') . ".\n\nVotre billet : {$p['code']}\nPrésentez-le à l'entrée, sur votre téléphone ou imprimé :\n$billet"],
        'recue' => ['Candidature reçue : ' . $p['titre'], $bonjour . "Votre candidature comme « {$p['role_libelle']} » pour « {$p['titre']} » a bien été reçue. L'équipe du chapitre l'étudie et vous répond par e-mail et dans votre espace."],
        'retenue' => ['Vous êtes retenu : ' . $p['titre'], $bonjour . "Bonne nouvelle : votre candidature comme « {$p['role_libelle']} » pour « {$p['titre']} » est retenue.\n\nVotre billet : {$p['code']}\nPrésentez-le à l'entrée :\n$billet\n\nL'équipe vous contactera pour la suite."],
        default => ['Votre candidature : ' . $p['titre'], $bonjour . "Merci pour votre candidature comme « {$p['role_libelle']} » pour « {$p['titre']} ». Elle n'a pas été retenue cette fois, mais d'autres initiatives arrivent : suivez-les sur le site."],
    };
    try {
        envoyer_email($p['email'], $sujet, $texte . "\n\nL'équipe YouthConnekt Mali");
    } catch (Throwable $e) {
        journaliser('erreurs.log', 'E-mail de participation non envoyé : ' . $e->getMessage());
    }
}
