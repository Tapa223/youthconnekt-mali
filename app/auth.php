<?php
/*
 * Comptes : connexion, droits de l'équipe d'administration, profils, liens par e-mail.
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// Membre connecté
// ---------------------------------------------------------------------------

function utilisateur(): ?array
{
    static $cache = false;
    if ($cache !== false) {
        return $cache;
    }
    $id = (int) ($_SESSION['utilisateur_id'] ?? 0);
    $cache = null;
    if ($id > 0) {
        $u = ligne('SELECT * FROM utilisateurs WHERE id = ? AND actif = 1', [$id]);
        // Si le mot de passe a changé ailleurs, la session est fermée
        if ($u && hash_equals((string) ($_SESSION['empreinte_mdp'] ?? ''), empreinte_session($u))) {
            $cache = completer_utilisateur($u);
        } else {
            unset($_SESSION['utilisateur_id'], $_SESSION['empreinte_mdp']);
        }
    }
    return $cache;
}

function empreinte_session(array $u): string
{
    return hash('sha256', $u['id'] . '|' . $u['mot_de_passe']);
}

/** Ajoute au compte son profil, son nom affiché et ses initiales. */
function completer_utilisateur(array $u): array
{
    $u['profil'] = profil_de($u);
    $p = $u['profil'];
    if ($p && $u['type_compte'] === 'organisation') {
        $u['nom_affiche'] = $p['nom_structure'];
        $u['prenom_affiche'] = $p['nom_structure'];
        $u['initiales'] = initiales_organisation($p['nom_structure']);
    } elseif ($p) {
        $u['nom_affiche'] = $p['prenom'] . ' ' . $p['nom'];
        $u['prenom_affiche'] = $p['prenom'];
        $u['initiales'] = mb_strtoupper(mb_substr($p['prenom'], 0, 1) . mb_substr($p['nom'], 0, 1));
    } else {
        $nom = trim(($u['prenom_admin'] ?? '') . ' ' . ($u['nom_admin'] ?? ''));
        $u['nom_affiche'] = $nom !== '' ? $nom : $u['email'];
        $u['prenom_affiche'] = $u['prenom_admin'] ?? '';
        $u['initiales'] = $nom !== '' ? initiales($nom) : mb_strtoupper(mb_substr($u['email'], 0, 1));
    }
    return $u;
}

function initiales_organisation(string $nom): string
{
    $mots = array_slice(array_values(array_filter(preg_split('/\s+/u', trim($nom)))), 0, 2);
    $r = '';
    foreach ($mots as $m) {
        $r .= mb_substr($m, 0, 1);
    }
    return mb_strtoupper($r) ?: '?';
}

function profil_de(array $u): ?array
{
    if ($u['type_compte'] === 'administration') {
        return null;
    }
    if ($u['type_compte'] === 'organisation') {
        return ligne('SELECT * FROM profils_organisations WHERE utilisateur_id = ?', [$u['id']]);
    }
    return ligne('SELECT * FROM profils_individuels WHERE utilisateur_id = ?', [$u['id']]);
}

function est_connecte(): bool
{
    return utilisateur() !== null;
}

function connecter(array $u, bool $se_souvenir = false): void
{
    session_regenerate_id(true);
    $_SESSION['utilisateur_id'] = (int) $u['id'];
    $_SESSION['empreinte_mdp'] = empreinte_session($u);
    unset($_SESSION['csrf']);
    if ($se_souvenir) {
        // Garder la session ouverte 30 jours
        $params = session_get_cookie_params();
        setcookie(session_name(), session_id(), [
            'expires' => time() + 30 * 86400,
            'path' => $params['path'],
            'secure' => $params['secure'],
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
    requete('UPDATE utilisateurs SET derniere_connexion = ? WHERE id = ?', [maintenant(), $u['id']]);
}

function deconnecter(): void
{
    $_SESSION = [];
    $params = session_get_cookie_params();
    setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $params['path'], 'secure' => $params['secure'], 'httponly' => true, 'samesite' => 'Lax']);
    session_destroy();
    demarrer_session();
    session_regenerate_id(true);
}

/** Exige un membre connecté, sinon renvoie vers la connexion puis revient ici. */
function exiger_connexion(): array
{
    $u = utilisateur();
    if (!$u) {
        rediriger(url('/compte/connexion', ['suivant' => adresse_actuelle()]));
    }
    return $u;
}

// ---------------------------------------------------------------------------
// Équipe d'administration
// ---------------------------------------------------------------------------

/** Compte membre (individuel ou organisation), par opposition à un compte d'administration. */
function est_membre(?array $u = null): bool
{
    $u = $u ?? utilisateur();
    return $u !== null && $u['type_compte'] !== 'administration';
}

/** Exige un compte membre : un compte d'administration est renvoyé vers l'administration. */
function exiger_membre(): array
{
    $u = exiger_connexion();
    if (!est_membre($u)) {
        flash('erreur', "Vous êtes connecté avec un compte d'administration. L'espace membre et les communautés sont réservés aux comptes membres.");
        rediriger(url('/gestion'));
    }
    return $u;
}

function est_admin(?array $u = null): bool
{
    $u = $u ?? utilisateur();
    return $u !== null && $u['type_compte'] === 'administration' && !empty($u['role_admin']) && isset(ROLES_ADMIN[$u['role_admin']]);
}

/**
 * Droits de chaque rôle sur chaque rubrique de l'administration :
 * 'modifier' = consulter, ajouter, modifier et supprimer ; 'voir' = consulter seulement.
 */
const DROITS_ROLES = [
    'editeur' => [
        'opportunites' => 'modifier', 'actualites' => 'modifier', 'initiatives' => 'modifier',
        'temoignages' => 'modifier', 'partenaires' => 'modifier', 'chiffres' => 'modifier',
        'ministre' => 'modifier', 'axes' => 'modifier', 'communautes' => 'voir',
    ],
    'moderation' => [
        'communautes' => 'modifier', 'demandes' => 'modifier',
        'membres' => 'voir', 'organisations' => 'voir', 'comptes' => 'voir',
    ],
    'membres' => [
        'membres' => 'modifier', 'organisations' => 'modifier', 'comptes' => 'modifier',
        'messages' => 'modifier', 'newsletter' => 'modifier', 'parametres' => 'modifier',
    ],
];

function peut(string $rubrique, string $action = 'voir', ?array $u = null): bool
{
    $u = $u ?? utilisateur();
    if (!est_admin($u)) {
        return false;
    }
    if ($u['role_admin'] === 'super') {
        return true;
    }
    $droit = DROITS_ROLES[$u['role_admin']][$rubrique] ?? null;
    if ($droit === null) {
        return false;
    }
    return $action === 'voir' || $droit === 'modifier';
}

function exiger_admin(): array
{
    $u = utilisateur();
    if (!$u) {
        rediriger(url('/compte/connexion', ['suivant' => adresse_actuelle()]));
    }
    if (!est_admin($u)) {
        page_erreur(403, 'Accès réservé', "L'administration est réservée à l'équipe de YouthConnekt Mali.");
    }
    return $u;
}

function exiger_droit(string $rubrique, string $action = 'voir'): void
{
    exiger_admin();
    if (!peut($rubrique, $action)) {
        page_erreur(403, 'Accès réservé', "Votre rôle ne donne pas accès à cette rubrique.");
    }
}

// ---------------------------------------------------------------------------
// Mots de passe et tentatives de connexion
// ---------------------------------------------------------------------------

/** Renvoie un message d'erreur, ou '' si le mot de passe convient. */
function verifier_mot_de_passe(string $mdp, string $email = ''): string
{
    if (mb_strlen($mdp) < 8) {
        return 'Le mot de passe doit contenir au moins 8 caractères.';
    }
    if (ctype_digit($mdp)) {
        return 'Le mot de passe ne peut pas contenir uniquement des chiffres.';
    }
    $courants = ['password', 'motdepasse', '12345678', 'azertyui', 'qwertyui', 'password1', 'youthconnekt', 'bamako2026', 'abcdefgh'];
    if (in_array(mb_strtolower($mdp), $courants, true)) {
        return 'Ce mot de passe est trop courant. Choisissez-en un autre.';
    }
    $local = mb_strtolower(explode('@', $email)[0] ?? '');
    if ($local !== '' && mb_strlen($local) >= 4 && str_contains(mb_strtolower($mdp), $local)) {
        return 'Le mot de passe ressemble trop à votre adresse e-mail.';
    }
    return '';
}

function cle_tentative(string $email): string
{
    return hash('sha256', mb_strtolower($email) . '|' . adresse_ip());
}

function trop_de_tentatives(string $email): bool
{
    requete('DELETE FROM tentatives_connexion WHERE moment < ?', [date('Y-m-d H:i:s', time() - 3600)]);
    $n = (int) valeur(
        'SELECT COUNT(*) FROM tentatives_connexion WHERE cle = ? AND moment > ?',
        [cle_tentative($email), date('Y-m-d H:i:s', time() - 15 * 60)]
    );
    return $n >= 5;
}

function noter_tentative(string $email): void
{
    inserer('tentatives_connexion', ['cle' => cle_tentative($email), 'moment' => maintenant()]);
}

function effacer_tentatives(string $email): void
{
    requete('DELETE FROM tentatives_connexion WHERE cle = ?', [cle_tentative($email)]);
}

// ---------------------------------------------------------------------------
// Liens envoyés par e-mail (confirmation, nouveau mot de passe)
// ---------------------------------------------------------------------------

function creer_jeton(int $utilisateur_id, string $type, int $heures): string
{
    requete('DELETE FROM jetons WHERE utilisateur_id = ? AND type = ?', [$utilisateur_id, $type]);
    $jeton = bin2hex(random_bytes(32));
    inserer('jetons', [
        'utilisateur_id' => $utilisateur_id,
        'type' => $type,
        'empreinte' => hash('sha256', $jeton),
        'expire_le' => date('Y-m-d H:i:s', time() + $heures * 3600),
        'cree_le' => maintenant(),
    ]);
    return $jeton;
}

/** Renvoie l'identifiant du compte si le lien est valide (sans le consommer). */
function lire_jeton(string $jeton, string $type): ?int
{
    if (!preg_match('/^[a-f0-9]{64}$/', $jeton)) {
        return null;
    }
    $l = ligne('SELECT utilisateur_id, expire_le FROM jetons WHERE empreinte = ? AND type = ?', [hash('sha256', $jeton), $type]);
    if (!$l || strtotime($l['expire_le']) < time()) {
        return null;
    }
    return (int) $l['utilisateur_id'];
}

function consommer_jeton(string $jeton): void
{
    requete('DELETE FROM jetons WHERE empreinte = ?', [hash('sha256', $jeton)]);
}

function envoyer_reinitialisation(array $u): void
{
    $jeton = creer_jeton((int) $u['id'], 'mot_de_passe', 2);
    $lien = url_absolue('/compte/mot-de-passe/' . $jeton);
    $texte = "Bonjour,\n\nVous avez demandé à réinitialiser le mot de passe de votre compte YouthConnekt Mali.\n\n"
        . "Choisissez un nouveau mot de passe en ouvrant ce lien (valable 2 heures) :\n" . $lien . "\n\n"
        . "Si vous n'êtes pas à l'origine de cette demande, ignorez ce message : votre mot de passe reste inchangé.\n\n"
        . "L'équipe YouthConnekt Mali";
    envoyer_email($u['email'], 'Réinitialisation de votre mot de passe · YouthConnekt Mali', $texte, $lien);
}

// ---------------------------------------------------------------------------
// Profils
// ---------------------------------------------------------------------------

function taux_completion(?array $profil, string $type): int
{
    if (!$profil) {
        return 0;
    }
    $champs = $type === 'organisation'
        ? ['nom_structure', 'logo', 'type_structure', 'region', 'ville', 'secteur', 'presentation', 'personne_contact', 'email_contact_public']
        : ['prenom', 'nom', 'photo', 'categorie', 'region', 'ville', 'domaine', 'presentation', 'telephone'];
    $remplis = 0;
    foreach ($champs as $c) {
        if (trim((string) ($profil[$c] ?? '')) !== '') {
            $remplis++;
        }
    }
    // Au moins un lien (réseau social, site…) compte comme un champ rempli
    if (!empty($profil['utilisateur_id']) && liens_profil((int) $profil['utilisateur_id'])) {
        $remplis++;
    }
    return (int) round(100 * $remplis / (count($champs) + 1));
}

/** Libellé court du profil : « Étudiant », « Association »... */
/** Liens publics d'un membre : [['reseau' => …, 'url' => …, 'nom' => …, 'trace' => …], …] */
function liens_profil(int $utilisateur_id): array
{
    try {
        $lignes = lignes('SELECT reseau, url FROM liens_profil WHERE utilisateur_id = ? ORDER BY ordre, id', [$utilisateur_id]);
    } catch (Throwable $e) {
        return [];
    }
    return array_map(fn ($l) => $l + ['nom' => RESEAUX_PROFIL[$l['reseau']][0] ?? 'Lien', 'trace' => RESEAUX_PROFIL[$l['reseau']][1] ?? RESEAUX_PROFIL['autre'][1]], $lignes);
}

function libelle_profil(?array $profil, string $type): string
{
    if (!$profil) {
        return '';
    }
    return $type === 'organisation'
        ? (TYPES_STRUCTURE[$profil['type_structure']] ?? '')
        : (CATEGORIES_INDIVIDUELLES[$profil['categorie']] ?? '');
}
