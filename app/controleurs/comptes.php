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
        ? ['nom_structure', 'type_structure', 'email', 'telephone_organisation']
        : ['prenom', 'nom', 'email', 'telephone'];
    $valeurs = array_fill_keys($champs, '') + ['conditions' => '', 'newsletter' => ''];
    $erreurs = [];

    if (est_post()) {
        foreach (array_keys($valeurs) as $c) {
            $valeurs[$c] = post($c);
        }
        $valeurs['email'] = mb_strtolower($valeurs['email']);
        $champ_tel = $organisation ? 'telephone_organisation' : 'telephone';
        $valeurs[$champ_tel] = telephone_saisi($champ_tel);
        if ($m = erreur_telephone($valeurs[$champ_tel], true, $organisation ? "Indiquez le numéro de téléphone de l'organisation." : 'Indiquez votre numéro de téléphone.')) $erreurs[$champ_tel] = $m;
        $mdp1 = (string) ($_POST['password1'] ?? '');
        // Anti-robots : champ piège, envoi trop rapide, ou trop d'inscriptions depuis la même connexion
        if (robot_probable(4)) {
            $erreurs['_envoi'] = "Votre envoi n'a pas pu aboutir. Patientez quelques secondes, puis réessayez.";
        } elseif (limite_atteinte('inscription', 5)) {
            $erreurs['_envoi'] = "Trop de inscriptions depuis cette connexion. Réessayez dans une heure.";
        }

        if ($organisation) {
            if ($valeurs['nom_structure'] === '') $erreurs['nom_structure'] = "Indiquez le nom de l'organisation.";
            if (!isset(TYPES_STRUCTURE[$valeurs['type_structure']])) $erreurs['type_structure'] = 'Choisissez le type de structure.';
        } else {
            if ($valeurs['prenom'] === '') $erreurs['prenom'] = 'Indiquez votre prénom.';
            if ($valeurs['nom'] === '') $erreurs['nom'] = 'Indiquez votre nom.';
        }

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
                    'email_confirme' => 1,
                    'actif' => 1,
                    'inscrit_le' => maintenant(),
                ]);
                // Le profil reste masqué tant que le membre n'a pas choisi de le rendre visible (Mon profil).
                // Région, profil, âge… se complètent ensuite (espace membre, ou au moment où une action en a besoin).
                $commun = ['utilisateur_id' => $id, 'region' => '', 'visible' => 0, 'cree_le' => maintenant(), 'modifie_le' => maintenant()];
                if ($organisation) {
                    inserer('profils_organisations', $commun + [
                        'nom_structure' => mb_substr($valeurs['nom_structure'], 0, 150),
                        'type_structure' => $valeurs['type_structure'],
                        'telephone_organisation' => mb_substr($valeurs['telephone_organisation'], 0, 30),
                    ]);
                } else {
                    inserer('profils_individuels', $commun + [
                        'prenom' => mb_substr($valeurs['prenom'], 0, 80),
                        'nom' => mb_substr($valeurs['nom'], 0, 80),
                        'categorie' => '',
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
            // Pas d'e-mail de confirmation : le compte est ouvert et la personne connectée tout de suite
            connecter(ligne('SELECT * FROM utilisateurs WHERE id = ?', [$id]));
            $_SESSION['bienvenue'] = true; // l'espace membre accueille la personne : un nouveau point rejoint le réseau
            rediriger(url('/compte/mon-espace'));
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
        'suivant' => $suivant,
        'contexte' => $contexte,
    ], 'auth'));
}

function deconnexion(): void
{
    deconnecter();
    // Passage vers la création d'un autre compte (Paramètres > Ajouter un compte)
    $vers = post('vers');
    if (in_array($vers, ['individuel', 'organisation'], true)) {
        flash('succes', 'Vous êtes déconnecté. Créez maintenant votre ' . ($vers === 'organisation' ? 'compte organisation.' : 'compte personnel.'));
        rediriger(url('/compte/inscription/' . $vers));
    }
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
