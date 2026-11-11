<?php
/*
 * Espace membre : tableau de bord, communautés, opportunités ciblées, profil, paramètres,
 * et profil public d'un membre.
 */

declare(strict_types=1);

/** Demandes du membre, identifiants de ses communautés et opportunités ouvertes qui le concernent. */
function donnees_membre(array $u): array
{
    $demandes = lignes(
        'SELECT d.*, c.nom AS communaute_nom, c.slug AS communaute_slug FROM demandes_adhesion d
         JOIN communautes c ON c.id = d.communaute_id WHERE d.utilisateur_id = ? ORDER BY d.demandee_le DESC',
        [$u['id']]
    );
    $ids = [];
    foreach ($demandes as $d) {
        if ($d['statut'] === 'acceptee') {
            $ids[] = (int) $d['communaute_id'];
        }
    }
    $opportunites = $ids ? opportunites_ouvertes(
        'AND id IN (SELECT opportunite_id FROM opportunite_communaute WHERE communaute_id IN (' . marqueurs($ids) . ')) ORDER BY date_limite IS NULL, date_limite, publie_le DESC',
        $ids
    ) : [];
    foreach ($opportunites as &$o) {
        $o['via'] = communaute_via((int) $o['id'], $ids);
    }
    return [$demandes, $ids, $opportunites];
}

/** Chiffres affichés en pastille dans le menu de l'espace : communautés, opportunités ouvertes, notifications non lues. */
function compteurs_espace(array $u): array
{
    [, $ids, $opportunites] = donnees_membre($u);
    return ['communautes' => count($ids), 'opportunites' => count($opportunites), 'notifications' => nb_notifications_non_lues($u)];
}

function vue_espace(string $nom, string $rubrique, string $titre, array $donnees, array $u): never
{
    afficher(vue('espace/' . $nom, $donnees + [
        'titre' => $titre . ' | YouthConnekt Mali',
        'noindex' => true,
        'rubrique' => $rubrique,
        'nb_notifs' => nb_notifications_non_lues($u),
        'compteurs' => compteurs_espace($u),
        'u' => $u,
        'profil' => $u['profil'],
    ]));
}

function tableau_de_bord(): void
{
    $u = exiger_membre();
    [$demandes, $ids, $opportunites] = donnees_membre($u);

    $mes_communautes = [];
    if ($ids) {
        foreach (lignes(SELECT_COMMUNAUTE . ' WHERE c.id IN (' . marqueurs($ids) . ') ORDER BY c.ordre, c.nom', $ids) as $c) {
            $c = communaute_preparer($c);
            $c['nb_opportunites'] = (int) valeur(
                "SELECT COUNT(*) FROM opportunites o JOIN opportunite_communaute oc ON oc.opportunite_id = o.id
                 WHERE oc.communaute_id = ? AND o.statut = 'publie' AND o.publie_le <= NOW() AND (o.date_limite IS NULL OR o.date_limite >= CURDATE())",
                [$c['id']]
            );
            $mes_communautes[] = $c;
        }
    }
    $exclues = array_map(fn ($d) => (int) $d['communaute_id'], $demandes);
    $suggestions = array_values(array_filter(communautes_actives(), fn ($c) => !in_array((int) $c['id'], $exclues, true)));
    usort($suggestions, fn ($a, $b) => $b['nb_membres'] <=> $a['nb_membres'] ?: $a['ordre'] <=> $b['ordre']);
    $evenements = actualites_publiees("AND date_evenement >= CURDATE() ORDER BY date_evenement");

    vue_espace('tableau_de_bord', 'tableau', 'Mon espace', [
        'types' => types_opportunite(),
        'completion' => taux_completion($u['profil'], $u['type_compte']),
        'mes_communautes' => $mes_communautes,
        'demandes_en_attente' => array_values(array_filter($demandes, fn ($d) => $d['statut'] === 'en_attente')),
        'opportunites' => array_slice($opportunites, 0, 3),
        'nb_opportunites' => count($opportunites),
        'evenements' => array_slice($evenements, 0, 3),
        'nb_evenements' => count($evenements),
        'suggestions' => array_slice($suggestions, 0, 3),
    ], $u);
}

/** Mes opportunités proposées et leur état (à valider, publiée, non publiée). */
function mes_propositions_liste(array $u): array
{
    return array_map('opportunite_preparer', lignes('SELECT * FROM opportunites WHERE propose_par = ? ORDER BY cree_le DESC', [$u['id']]));
}

/** Notifications : la liste complète ; les ouvrir les marque comme lues. */
function notifications(): void
{
    $u = exiger_membre();
    $liste = notifications_membre($u);
    $vues_le = notifications_vues_le($u);
    try {
        requete('UPDATE utilisateurs SET notifications_vues_le = ? WHERE id = ?', [maintenant(), $u['id']]);
    } catch (Throwable $e) {
        // Colonne absente tant que la mise à jour 9 n'est pas importée : la liste s'affiche quand même
    }
    vue_espace('notifications', 'notifications', 'Notifications', ['notifications' => $liste, 'vues_le' => $vues_le], $u);
}

/** Le témoignage du membre (un seul, modifiable), ou null. */
function mon_temoignage(array $u): ?array
{
    try {
        return ligne('SELECT * FROM temoignages WHERE utilisateur_id = ? ORDER BY id DESC LIMIT 1', [$u['id']]);
    } catch (Throwable $e) {
        return null; // mise à jour 10 pas encore importée
    }
}

/** Envoi ou modification du témoignage depuis « Mes propositions » : il repasse en relecture par l'équipe. */
function envoyer_temoignage(): void
{
    $u = exiger_membre();
    $texte = trim(post('texte'));
    $retour = url('/compte/mon-espace/propositions') . '#temoignage';
    if (mb_strlen($texte) < 20 || mb_strlen($texte) > 600) {
        flash('erreur', mb_strlen($texte) < 20 ? 'Votre témoignage est trop court : quelques phrases suffisent (20 caractères au minimum).' : '600 caractères au maximum.');
        $_SESSION['brouillon_temoignage'] = $texte;
        rediriger($retour);
    }
    if (post('accord') === '') {
        flash('erreur', 'Cochez la case pour accepter que votre témoignage soit publié.');
        $_SESSION['brouillon_temoignage'] = $texte;
        rediriger($retour);
    }
    $p = $u['profil'] ?? [];
    $role = implode(', ', array_filter([
        ($u['type_compte'] === 'organisation' ? ($p['secteur'] ?? '') : ($p['domaine'] ?? '')) ?: libelle_profil($p, $u['type_compte']),
        ($p['ville'] ?? '') ?: (REGIONS[$p['region'] ?? ''] ?? ''),
    ]));
    // La photo du profil est recopiée : le témoignage la garde même si le membre change la sienne
    $photo = '';
    $source = $p['photo'] ?? $p['logo'] ?? '';
    if ($source && is_file(RACINE . '/' . $source)) {
        $dossier = RACINE . '/uploads/temoignages';
        if (!is_dir($dossier)) @mkdir($dossier, 0775, true);
        $nom = 'temoignage-' . $u['id'] . '-' . time() . '.' . pathinfo($source, PATHINFO_EXTENSION);
        if (@copy(RACINE . '/' . $source, $dossier . '/' . $nom)) $photo = 'uploads/temoignages/' . $nom;
    }
    $donnees = ['texte' => mb_substr($texte, 0, 600), 'nom' => mb_substr($u['nom_affiche'], 0, 120), 'role' => mb_substr($role, 0, 150),
        'photo' => $photo, 'publie' => 0, 'envoye_le' => maintenant()];
    if ($u['type_compte'] === 'organisation') {
        $donnees['categorie'] = 'organisation';
    }
    $ancien = mon_temoignage($u);
    if ($ancien) {
        mettre_a_jour('temoignages', $donnees, (int) $ancien['id']);
        supprimer_image($ancien['photo']);
    } else {
        inserer('temoignages', $donnees + ['utilisateur_id' => $u['id'], 'ordre' => 0]);
    }
    unset($_SESSION['brouillon_temoignage']);
    flash('succes', "Merci ! Votre témoignage a bien été envoyé. L'équipe le relit avant de l'afficher sur le site.");
    rediriger($retour);
}

function mes_propositions(): void
{
    $u = exiger_membre();
    $initiatives = lignes("SELECT * FROM messages_contact WHERE email = ? AND objet LIKE 'Proposition d''initiative%' ORDER BY recu_le DESC", [$u['email']]);
    $brouillon = (string) ($_SESSION['brouillon_temoignage'] ?? '');
    unset($_SESSION['brouillon_temoignage']);
    vue_espace('propositions', 'propositions', 'Mes propositions', [
        'temoignage' => mon_temoignage($u),
        'brouillon_temoignage' => $brouillon,
        'propositions' => mes_propositions_liste($u),
        'initiatives' => $initiatives,
        'types' => types_opportunite(),
    ], $u);
}

function mes_communautes(): void
{
    $u = exiger_membre();
    [$demandes] = donnees_membre($u);
    vue_espace('communautes', 'communautes', 'Mes communautés', ['demandes' => $demandes], $u);
}

function mes_opportunites(): void
{
    $u = exiger_membre();
    [, $ids, $opportunites] = donnees_membre($u);
    vue_espace('opportunites', 'opportunites', 'Mes opportunités', ['opportunites' => $opportunites, 'a_des_communautes' => (bool) $ids, 'types' => types_opportunite()], $u);
}

function mon_profil(): void
{
    $u = exiger_membre();
    $organisation = $u['type_compte'] === 'organisation';
    $table = $organisation ? 'profils_organisations' : 'profils_individuels';
    $champ_image = $organisation ? 'logo' : 'photo';
    $profil = $u['profil'] ?? [];
    $champs = $organisation
        ? ['nom_structure', 'type_structure', 'secteur', 'region', 'ville', 'presentation', 'email_contact_public', 'personne_contact']
        : ['prenom', 'nom', 'genre', 'date_naissance', 'categorie', 'domaine', 'region', 'ville', 'presentation', 'telephone'];
    $valeurs = [];
    foreach ($champs as $c) {
        $valeurs[$c] = (string) ($profil[$c] ?? '');
    }
    if (!$valeurs['region']) {
        $valeurs['region'] = 'bamako';
    }
    $liens = array_map(fn ($l) => ['reseau' => $l['reseau'], 'url' => $l['url']], liens_profil((int) $u['id']));
    $erreurs = [];

    // Rendre le profil visible ou le masquer (action séparée, confirmée par le membre)
    if (est_post() && post('action') === 'visibilite') {
        if ($profil) {
            $visible = post('visible') === '1' ? 1 : 0;
            mettre_a_jour($table, ['visible' => $visible, 'modifie_le' => maintenant()], (int) $profil['id']);
            flash('succes', $visible ? 'Votre profil est maintenant visible des membres de vos communautés.' : "Votre profil est masqué : il n'apparaît plus dans les communautés.");
        }
        rediriger(url('/compte/mon-espace/profil') . '#apercu');
    }

    if (est_post()) {
        foreach ($champs as $c) {
            $valeurs[$c] = post($c);
        }
        // Liens : un réseau et une adresse par ligne, 8 au plus
        $liens = [];
        $reseaux = (array) ($_POST['lien_reseau'] ?? []);
        foreach ((array) ($_POST['lien_url'] ?? []) as $k => $adresse) {
            $adresse = trim((string) $adresse);
            if ($adresse === '') {
                continue;
            }
            $reseau = (string) ($reseaux[$k] ?? 'autre');
            if (!isset(RESEAUX_PROFIL[$reseau])) {
                $reseau = 'autre';
            }
            if (!preg_match('#^https?://#i', $adresse)) {
                $adresse = 'https://' . $adresse;
            }
            if (!filter_var($adresse, FILTER_VALIDATE_URL) || mb_strlen($adresse) > 255) {
                $erreurs['liens'] = "L'adresse « " . mb_substr($adresse, 0, 60) . " » n'est pas valide.";
            }
            $liens[] = ['reseau' => $reseau, 'url' => $adresse];
        }
        if (count($liens) > 8) {
            $erreurs['liens'] = '8 liens au maximum.';
        }
        $limites = ['prenom' => 80, 'nom' => 80, 'domaine' => 150, 'ville' => 100, 'presentation' => 1500, 'telephone' => 30,
            'nom_structure' => 150, 'secteur' => 150, 'personne_contact' => 120];
        foreach ($limites as $c => $max) {
            if (isset($valeurs[$c]) && mb_strlen($valeurs[$c]) > $max) {
                $erreurs[$c] = "$max caractères au maximum.";
            }
        }
        if ($organisation) {
            if ($valeurs['nom_structure'] === '') $erreurs['nom_structure'] = "Indiquez le nom de l'organisation.";
            if (!isset(TYPES_STRUCTURE[$valeurs['type_structure']])) $erreurs['type_structure'] = 'Choisissez le type de structure.';
            if ($valeurs['email_contact_public'] !== '' && !filter_var($valeurs['email_contact_public'], FILTER_VALIDATE_EMAIL)) {
                $erreurs['email_contact_public'] = "Cette adresse e-mail n'est pas valide.";
            }
        } else {
            if ($valeurs['prenom'] === '') $erreurs['prenom'] = 'Indiquez votre prénom.';
            if ($valeurs['nom'] === '') $erreurs['nom'] = 'Indiquez votre nom.';
            if (!isset(GENRES[$valeurs['genre']])) $erreurs['genre'] = 'Choisissez une réponse.';
            if ($m = erreur_naissance($valeurs['date_naissance'])) $erreurs['date_naissance'] = $m;
            if (!isset(CATEGORIES_INDIVIDUELLES[$valeurs['categorie']])) $erreurs['categorie'] = 'Choisissez votre profil.';
        }
        if (!isset(REGIONS[$valeurs['region']])) $erreurs['region'] = 'Choisissez votre région.';
        $nouvelle_image = null;
        if (!$erreurs) {
            try {
                $nouvelle_image = televerser_image($_FILES[$champ_image] ?? null, $organisation ? 'organisations' : 'profils', 800);
            } catch (RuntimeException $e) {
                $erreurs[$champ_image] = $e->getMessage();
            }
        }
        if (!$erreurs) {
            $donnees = $valeurs + ['modifie_le' => maintenant()];
            $ancienne = $profil[$champ_image] ?? '';
            if ($nouvelle_image) {
                $donnees[$champ_image] = $nouvelle_image;
            } elseif (post('supprimer_image') !== '') {
                $donnees[$champ_image] = '';
            }
            if ($profil) {
                mettre_a_jour($table, $donnees, (int) $profil['id']);
            } else {
                inserer($table, $donnees + ['utilisateur_id' => $u['id'], 'visible' => 0, 'cree_le' => maintenant()]);
            }
            requete('DELETE FROM liens_profil WHERE utilisateur_id = ?', [$u['id']]);
            foreach ($liens as $k => $l) {
                inserer('liens_profil', ['utilisateur_id' => $u['id'], 'reseau' => $l['reseau'], 'url' => $l['url'], 'ordre' => $k + 1]);
            }
            if ($ancienne && isset($donnees[$champ_image]) && $donnees[$champ_image] !== $ancienne) {
                supprimer_image($ancienne);
            }
            // On revient sur l'aperçu du profil (et non en bas de page), formulaire refermé
            rediriger(url('/compte/mon-espace/profil', ['enregistre' => 1]) . '#apercu');
        }
    }

    vue_espace('profil', 'profil', 'Mon profil', [
        'organisation' => $organisation,
        'liens' => $liens,
        'visible' => (bool) ($profil['visible'] ?? false),
        'valeurs' => $valeurs,
        'erreurs' => $erreurs,
        'champ_image' => $champ_image,
        'image_actuelle' => $profil[$champ_image] ?? '',
    ], $u);
}

function parametres_compte(): void
{
    $u = exiger_membre();
    $erreurs = [];
    if (est_post()) {
        $action = post('action');
        $actuel = (string) ($_POST['mot_de_passe_actuel'] ?? '');
        if (!password_verify($actuel, $u['mot_de_passe'])) {
            $erreurs[$action . '_actuel'] = $actuel === '' ? 'Saisissez votre mot de passe actuel.' : 'Mot de passe incorrect.';
        } elseif ($action === 'mdp') {
            $mdp1 = (string) ($_POST['password1'] ?? '');
            if (($msg = verifier_mot_de_passe($mdp1, $u['email'])) !== '') {
                $erreurs['password1'] = $msg;
            } elseif ($mdp1 !== (string) ($_POST['password2'] ?? '')) {
                $erreurs['password2'] = 'Les deux mots de passe ne correspondent pas.';
            } else {
                $hash = password_hash($mdp1, PASSWORD_DEFAULT);
                requete('UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?', [$hash, $u['id']]);
                $u['mot_de_passe'] = $hash;
                connecter($u);
                flash('succes', 'Votre mot de passe a bien été modifié.');
                rediriger(url('/compte/mon-espace/parametres') . '#mot-de-passe');
            }
        } elseif ($action === 'supprimer') {
            if ($u['role_admin'] === 'super' && (int) valeur("SELECT COUNT(*) FROM utilisateurs WHERE role_admin = 'super' AND actif = 1") <= 1) {
                $erreurs['supprimer_actuel'] = "Vous êtes le seul administrateur principal : nommez-en un autre avant de supprimer votre compte.";
            } else {
                $profil = $u['profil'];
                requete('DELETE FROM utilisateurs WHERE id = ?', [$u['id']]);
                supprimer_image($profil['photo'] ?? $profil['logo'] ?? null);
                deconnecter();
                flash('succes', 'Votre compte et vos informations ont été supprimés.');
                rediriger(url('/'));
            }
        }
    }
    vue_espace('parametres', 'parametres', 'Paramètres', ['erreurs' => $erreurs], $u);
}

function profil_public(int $id): void
{
    $moi = exiger_connexion();
    $m = ligne('SELECT * FROM utilisateurs WHERE id = ? AND actif = 1', [$id]);
    if (!$m) {
        page_erreur(404);
    }
    $m = completer_utilisateur($m);
    $p = $m['profil'];
    if (!$p || (!$p['visible'] && (int) $m['id'] !== (int) $moi['id'] && !est_admin($moi))) {
        page_erreur(404);
    }
    $communautes = array_map('communaute_preparer', lignes(
        "SELECT c.* FROM communautes c JOIN demandes_adhesion d ON d.communaute_id = c.id
         WHERE d.utilisateur_id = ? AND d.statut = 'acceptee' AND c.active = 1 ORDER BY c.ordre",
        [$m['id']]
    ));
    // Parcours au sein du chapitre : initiatives (inscrit ou retenu), présences, contributions publiées
    $parcours = lignes(
        "SELECT p.statut, p.present_le, COALESCE(r.nom, p.role_nom) AS role_libelle, i.titre, i.slug, i.date_libelle, i.etat
         FROM participations p JOIN initiatives i ON i.id = p.initiative_id LEFT JOIN initiative_roles r ON r.id = p.role_id
         WHERE p.utilisateur_id = ? AND p.statut IN ('inscrit', 'retenue') AND i.statut = 'publie' ORDER BY p.cree_le DESC",
        [$m['id']]
    );
    $contributions = (int) valeur("SELECT COUNT(*) FROM opportunites WHERE propose_par = ? AND statut = 'publie'", [$m['id']]);
    afficher(vue('espace/profil_public', [
        'titre' => $m['nom_affiche'] . ' | Communauté | YouthConnekt Mali',
        'noindex' => true,
        'm' => $m,
        'p' => $p,
        'communautes' => $communautes,
        'parcours' => $parcours,
        'contributions' => $contributions,
        'est_moi' => (int) $m['id'] === (int) $moi['id'],
    ]));
}
