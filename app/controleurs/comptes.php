<?php
/*
 * Comptes : inscription en trois étapes, confirmation de l'e-mail, connexion,
 * déconnexion et mot de passe oublié.
 */

declare(strict_types=1);

function rediriger_si_connecte(): void
{
    if (est_connecte()) {
        rediriger(url('/compte/mon-espace'));
    }
}

// ---------------------------------------------------------------------------
// Inscription
// ---------------------------------------------------------------------------

/** Étape 1 : choix du type de compte */
function inscription(): void
{
    rediriger_si_connecte();
    afficher(vue('comptes/inscription_type', [
        'titre' => 'Rejoindre le chapitre | YouthConnekt Mali',
        'etape' => 1,
        'largeur' => 'max-w-2xl',
    ], 'auth'));
}

/** Étape 2 : informations du compte */
function inscription_formulaire(string $slug): void
{
    rediriger_si_connecte();
    if (!in_array($slug, ['individuel', 'organisation'], true)) {
        page_erreur(404);
    }
    $organisation = $slug === 'organisation';
    $champs = $organisation
        ? ['nom_structure', 'type_structure', 'personne_contact', 'region', 'email', 'site_web']
        : ['prenom', 'nom', 'categorie', 'region', 'email', 'telephone'];
    $valeurs = array_fill_keys($champs, '') + ['region' => 'bamako', 'conditions' => '', 'newsletter' => ''];
    $erreurs = [];

    if (est_post()) {
        foreach (array_keys($valeurs) as $c) {
            $valeurs[$c] = post($c);
        }
        $valeurs['email'] = mb_strtolower($valeurs['email']);
        $mdp1 = (string) ($_POST['password1'] ?? '');
        $mdp2 = (string) ($_POST['password2'] ?? '');

        if ($organisation) {
            if ($valeurs['nom_structure'] === '') $erreurs['nom_structure'] = "Indiquez le nom de l'organisation.";
            if (!isset(TYPES_STRUCTURE[$valeurs['type_structure']])) $erreurs['type_structure'] = 'Choisissez le type de structure.';
            if ($valeurs['personne_contact'] === '') $erreurs['personne_contact'] = 'Indiquez la personne de contact.';
            if ($valeurs['site_web'] !== '') {
                if (!preg_match('#^https?://#i', $valeurs['site_web'])) {
                    $valeurs['site_web'] = 'https://' . $valeurs['site_web'];
                }
                if (!filter_var($valeurs['site_web'], FILTER_VALIDATE_URL)) $erreurs['site_web'] = "Cette adresse web n'est pas valide.";
            }
        } else {
            if ($valeurs['prenom'] === '') $erreurs['prenom'] = 'Indiquez votre prénom.';
            if ($valeurs['nom'] === '') $erreurs['nom'] = 'Indiquez votre nom.';
            if (!isset(CATEGORIES_INDIVIDUELLES[$valeurs['categorie']])) $erreurs['categorie'] = 'Choisissez votre profil.';
        }
        if (!isset(REGIONS[$valeurs['region']])) $erreurs['region'] = 'Choisissez votre région.';

        if ($valeurs['email'] === '') {
            $erreurs['email'] = 'Indiquez votre adresse e-mail.';
        } elseif (!filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($valeurs['email']) > 190) {
            $erreurs['email'] = "Cette adresse e-mail n'est pas valide.";
        } elseif (valeur('SELECT id FROM utilisateurs WHERE email = ?', [$valeurs['email']])) {
            $erreurs['email'] = 'Un compte existe déjà avec cette adresse. Connectez-vous ou utilisez « Mot de passe oublié ».';
        }

        if ($mdp1 === '') {
            $erreurs['password1'] = 'Choisissez un mot de passe.';
        } elseif (($msg = verifier_mot_de_passe($mdp1, $valeurs['email'])) !== '') {
            $erreurs['password1'] = $msg;
        }
        if ($mdp2 === '') {
            $erreurs['password2'] = 'Confirmez votre mot de passe.';
        } elseif ($mdp1 !== $mdp2) {
            $erreurs['password2'] = 'Les deux mots de passe ne correspondent pas.';
        }
        if ($valeurs['conditions'] === '') {
            $erreurs['conditions'] = 'Vous devez accepter la politique de confidentialité pour créer un compte.';
        }

        if (!$erreurs) {
            bdd()->beginTransaction();
            try {
                $id = inserer('utilisateurs', [
                    'email' => $valeurs['email'],
                    'mot_de_passe' => password_hash($mdp1, PASSWORD_DEFAULT),
                    'type_compte' => $slug,
                    'email_confirme' => 0,
                    'actif' => 1,
                    'inscrit_le' => maintenant(),
                ]);
                $commun = ['utilisateur_id' => $id, 'region' => $valeurs['region'], 'cree_le' => maintenant(), 'modifie_le' => maintenant()];
                if ($organisation) {
                    inserer('profils_organisations', $commun + [
                        'nom_structure' => mb_substr($valeurs['nom_structure'], 0, 150),
                        'type_structure' => $valeurs['type_structure'],
                        'personne_contact' => mb_substr($valeurs['personne_contact'], 0, 120),
                        'site_web' => mb_substr($valeurs['site_web'], 0, 255),
                    ]);
                } else {
                    inserer('profils_individuels', $commun + [
                        'prenom' => mb_substr($valeurs['prenom'], 0, 80),
                        'nom' => mb_substr($valeurs['nom'], 0, 80),
                        'categorie' => $valeurs['categorie'],
                        'telephone' => mb_substr($valeurs['telephone'], 0, 30),
                    ]);
                }
                if ($valeurs['newsletter'] !== '') {
                    requete('INSERT INTO abonnes_newsletter (email, inscrit_le, actif) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE actif = 1', [$valeurs['email'], maintenant()]);
                }
                bdd()->commit();
            } catch (Throwable $e) {
                bdd()->rollBack();
                throw $e;
            }
            envoyer_confirmation(['id' => $id, 'email' => $valeurs['email']]);
            $_SESSION['inscription_utilisateur'] = $id;
            rediriger(url('/compte/inscription/verification'));
        }
    }

    afficher(vue('comptes/inscription_formulaire', [
        'titre' => 'Vos informations | Rejoindre le chapitre | YouthConnekt Mali',
        'etape' => 2,
        'largeur' => 'max-w-2xl',
        'type_compte' => $slug,
        'valeurs' => $valeurs,
        'erreurs' => $erreurs,
    ], 'auth'));
}

function utilisateur_en_attente(): ?array
{
    $id = (int) ($_SESSION['inscription_utilisateur'] ?? 0);
    return $id ? ligne('SELECT * FROM utilisateurs WHERE id = ? AND email_confirme = 0', [$id]) : null;
}

/** Étape 3 : invitation à confirmer l'adresse e-mail */
function verification(): void
{
    $u = utilisateur_en_attente();
    if (!$u) {
        rediriger(url('/compte/connexion'));
    }
    afficher(vue('comptes/verification', [
        'titre' => 'Vérifiez votre e-mail | YouthConnekt Mali',
        'etape' => 3,
        'largeur' => 'max-w-2xl',
        'email' => $u['email'],
    ], 'auth'));
}

function renvoyer_confirmation(): void
{
    $u = utilisateur_en_attente();
    if (!$u) {
        rediriger(url('/compte/connexion'));
    }
    if (time() - (int) ($_SESSION['confirmation_envoyee_a'] ?? 0) < 60) {
        flash('erreur', 'Patientez une minute avant de demander un nouvel e-mail.');
    } else {
        envoyer_confirmation($u);
        flash('succes', 'Un nouvel e-mail de confirmation vient de vous être envoyé.');
    }
    rediriger(url('/compte/inscription/verification'));
}

function confirmer(string $jeton): void
{
    $id = lire_jeton($jeton, 'confirmation');
    $u = $id ? ligne('SELECT * FROM utilisateurs WHERE id = ? AND actif = 1', [$id]) : null;
    if (!$u) {
        http_response_code(400);
        afficher(vue('comptes/lien_invalide', ['titre' => 'Lien invalide | YouthConnekt Mali'], 'auth'));
    }
    consommer_jeton($jeton);
    requete('UPDATE utilisateurs SET email_confirme = 1 WHERE id = ?', [$u['id']]);
    unset($_SESSION['inscription_utilisateur']);
    $u['email_confirme'] = 1;
    connecter($u);
    flash('succes', 'Votre adresse e-mail est confirmée. Bienvenue dans votre espace.');
    rediriger(url('/compte/mon-espace'));
}

// ---------------------------------------------------------------------------
// Connexion
// ---------------------------------------------------------------------------

function connexion(): void
{
    $suivant = adresse_retour('');
    if (est_connecte()) {
        rediriger($suivant ?: url('/compte/mon-espace'));
    }
    $email = '';
    $erreur = '';
    $non_confirme = false;

    if (est_post()) {
        $email = mb_strtolower(post('email'));
        $mdp = (string) ($_POST['mot_de_passe'] ?? '');
        if ($email === '' || $mdp === '') {
            $erreur = 'Indiquez votre adresse e-mail et votre mot de passe.';
        } elseif (trop_de_tentatives($email)) {
            $erreur = 'Trop de tentatives. Patientez quinze minutes ou utilisez « Mot de passe oublié ».';
        } else {
            $u = ligne('SELECT * FROM utilisateurs WHERE email = ?', [$email]);
            if (!$u || !password_verify($mdp, $u['mot_de_passe'])) {
                noter_tentative($email);
                $erreur = 'Adresse e-mail ou mot de passe incorrect.';
            } elseif (!$u['actif']) {
                $erreur = 'Ce compte a été désactivé. Contactez le chapitre pour en savoir plus.';
            } elseif (!$u['email_confirme'] && $u['type_compte'] !== 'administration') {
                $erreur = "Confirmez d'abord votre adresse e-mail grâce au lien reçu par e-mail.";
                $non_confirme = true;
                $_SESSION['inscription_utilisateur'] = (int) $u['id'];
            } else {
                effacer_tentatives($email);
                if (password_needs_rehash($u['mot_de_passe'], PASSWORD_DEFAULT)) {
                    $u['mot_de_passe'] = password_hash($mdp, PASSWORD_DEFAULT);
                    requete('UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?', [$u['mot_de_passe'], $u['id']]);
                }
                connecter($u, !empty($_POST['se_souvenir']));
                $defaut = $u['type_compte'] === 'administration' ? url('/gestion') : url('/compte/mon-espace');
                rediriger($suivant ?: $defaut);
            }
        }
    }

    // Message selon la page d'où l'on vient (« Demander à intégrer » une communauté...)
    $contexte = '';
    $base = base_url();
    if ($suivant !== '' && preg_match('#^' . preg_quote($base, '#') . '/communaute/([a-z0-9\-]+)#', $suivant, $m)) {
        $nom = valeur('SELECT nom FROM communautes WHERE slug = ?', [$m[1]]);
        if ($nom) {
            $contexte = 'Connectez-vous pour demander à intégrer la communauté « ' . $nom . ' ». Vous y reviendrez juste après.';
        }
    } elseif ($suivant !== '' && strpos($suivant, $base . '/gestion') === 0) {
        $contexte = "Connectez-vous avec votre compte d'administration.";
    }

    afficher(vue('comptes/connexion', [
        'titre' => 'Se connecter | YouthConnekt Mali',
        'accroche' => 'Retrouvez vos opportunités, la communauté et votre espace membre.',
        'accroche_mobile' => 'Retrouvez vos opportunités, la communauté et votre espace membre.',
        'email' => $email,
        'erreur' => $erreur,
        'non_confirme' => $non_confirme,
        'suivant' => $suivant,
        'contexte' => $contexte,
    ], 'auth'));
}

function deconnexion(): void
{
    deconnecter();
    flash('succes', 'Vous êtes déconnecté. À bientôt.');
    rediriger(url('/'));
}

// ---------------------------------------------------------------------------
// Mot de passe oublié
// ---------------------------------------------------------------------------

function mdp_oublie(): void
{
    $email = '';
    $erreur = '';
    if (est_post()) {
        $email = mb_strtolower(post('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreur = "Cette adresse e-mail n'est pas valide.";
        } else {
            $u = ligne('SELECT * FROM utilisateurs WHERE email = ? AND actif = 1', [$email]);
            // Même suite dans tous les cas, sans révéler si un compte existe
            if ($u && time() - (int) ($_SESSION['reinitialisation_envoyee_a'] ?? 0) > 60) {
                envoyer_reinitialisation($u);
                $_SESSION['reinitialisation_envoyee_a'] = time();
            }
            rediriger(url('/compte/mot-de-passe-oublie/envoye'));
        }
    }
    afficher(vue('comptes/mdp_oublie', ['titre' => 'Mot de passe oublié | YouthConnekt Mali', 'email' => $email, 'erreur' => $erreur], 'auth'));
}

function mdp_envoye(): void
{
    afficher(vue('comptes/mdp_envoye', ['titre' => 'E-mail envoyé | YouthConnekt Mali'], 'auth'));
}

function mdp_nouveau(string $jeton): void
{
    $id = lire_jeton($jeton, 'mot_de_passe');
    $u = $id ? ligne('SELECT * FROM utilisateurs WHERE id = ? AND actif = 1', [$id]) : null;
    $erreurs = [];
    if ($u && est_post()) {
        $mdp1 = (string) ($_POST['password1'] ?? '');
        $mdp2 = (string) ($_POST['password2'] ?? '');
        if (($msg = verifier_mot_de_passe($mdp1, $u['email'])) !== '') {
            $erreurs['password1'] = $msg;
        } elseif ($mdp1 !== $mdp2) {
            $erreurs['password2'] = 'Les deux mots de passe ne correspondent pas.';
        }
        if (!$erreurs) {
            // Ouvrir le lien prouve aussi que l'adresse e-mail est la bonne
            requete('UPDATE utilisateurs SET mot_de_passe = ?, email_confirme = 1 WHERE id = ?', [password_hash($mdp1, PASSWORD_DEFAULT), $u['id']]);
            consommer_jeton($jeton);
            effacer_tentatives($u['email']);
            rediriger(url('/compte/mot-de-passe-modifie'));
        }
    }
    afficher(vue('comptes/mdp_nouveau', ['titre' => 'Nouveau mot de passe | YouthConnekt Mali', 'lien_valide' => (bool) $u, 'erreurs' => $erreurs], 'auth'));
}

function mdp_modifie(): void
{
    afficher(vue('comptes/mdp_modifie', ['titre' => 'Mot de passe modifié | YouthConnekt Mali'], 'auth'));
}
