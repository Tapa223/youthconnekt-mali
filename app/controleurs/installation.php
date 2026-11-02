<?php
/*
 * Installation : crée les tables, les données de départ et le premier compte administrateur.
 * Cette page ne fonctionne qu'une fois : dès qu'un administrateur principal existe, elle disparaît.
 */

declare(strict_types=1);

function executer_fichier_sql(string $fichier): void
{
    $sql = (string) file_get_contents($fichier);
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);
    foreach (preg_split('/;\s*(\r?\n|$)/', $sql) as $instruction) {
        if (trim($instruction) !== '') {
            bdd()->exec($instruction);
        }
    }
}

function installer(): void
{
    $erreur_bdd = '';
    try {
        bdd();
    } catch (PDOException $e) {
        $erreur_bdd = $e->getMessage();
    }

    if ($erreur_bdd === '' && base_installee()) {
        page_erreur(404);
    }

    $valeurs = ['prenom' => '', 'nom' => '', 'email' => ''];
    $erreurs = [];

    if ($erreur_bdd === '' && est_post()) {
        $valeurs = ['prenom' => post('prenom'), 'nom' => post('nom'), 'email' => mb_strtolower(post('email'))];
        $mdp = (string) ($_POST['mot_de_passe'] ?? '');
        if ($valeurs['prenom'] === '') {
            $erreurs['prenom'] = 'Indiquez votre prénom.';
        }
        if (!filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = 'Cette adresse e-mail n\'est pas valide.';
        }
        if (($msg = verifier_mot_de_passe($mdp, $valeurs['email'])) !== '') {
            $erreurs['mot_de_passe'] = $msg;
        } elseif ($mdp !== (string) ($_POST['mot_de_passe2'] ?? '')) {
            $erreurs['mot_de_passe2'] = 'Les deux mots de passe ne correspondent pas.';
        }

        if (!$erreurs) {
            executer_fichier_sql(RACINE . '/base-de-donnees/schema.sql');
            executer_fichier_sql(RACINE . '/base-de-donnees/donnees-depart.sql');
            $existant = ligne('SELECT id FROM utilisateurs WHERE email = ?', [$valeurs['email']]);
            $donnees = [
                'mot_de_passe' => password_hash($mdp, PASSWORD_DEFAULT),
                'email_confirme' => 1,
                'actif' => 1,
                'role_admin' => 'super',
                'prenom_admin' => $valeurs['prenom'],
                'nom_admin' => $valeurs['nom'],
            ];
            if ($existant) {
                mettre_a_jour('utilisateurs', $donnees, (int) $existant['id']);
                $id = (int) $existant['id'];
            } else {
                $id = inserer('utilisateurs', $donnees + ['email' => $valeurs['email'], 'type_compte' => 'individuel', 'inscrit_le' => maintenant()]);
            }
            if (!empty($_POST['exemples'])) {
                require_once RACINE . '/app/exemples.php';
                charger_exemples();
            }
            connecter(ligne('SELECT * FROM utilisateurs WHERE id = ?', [$id]));
            flash('succes', 'Installation terminée. Bienvenue dans l\'administration de YouthConnekt Mali.');
            rediriger(url('/gestion'));
        }
    }

    afficher(vue('pages/installation', [
        'titre' => 'Installation | YouthConnekt Mali',
        'erreur_bdd' => $erreur_bdd,
        'valeurs' => $valeurs,
        'erreurs' => $erreurs,
    ], 'auth'));
}
