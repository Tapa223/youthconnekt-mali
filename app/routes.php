<?php
/*
 * Adresses du site. Chaque adresse renvoie vers une fonction d'un fichier de app/controleurs/.
 * {slug} = texte de l'adresse (lettres, chiffres, tirets) ; {id} = nombre ; {jeton} = lien reçu par e-mail.
 */

declare(strict_types=1);

const ROUTES = [
    // Pages publiques
    ['GET', '/', 'site', 'accueil'],
    ['GET', '/a-propos', 'site', 'a_propos'],
    ['GET|POST', '/contact', 'site', 'contact'],
    ['GET', '/confidentialite', 'site', 'confidentialite'],
    ['GET', '/mentions-legales', 'site', 'mentions_legales'],
    ['POST', '/newsletter', 'site', 'newsletter'],
    ['GET', '/initiatives', 'contenus', 'initiatives'],
    ['GET', '/initiatives/{slug}', 'contenus', 'initiative'],
    ['GET', '/opportunites', 'contenus', 'opportunites'],
    ['GET', '/opportunites/{slug}', 'contenus', 'opportunite'],
    ['GET', '/actualites', 'contenus', 'actualites'],
    ['GET', '/actualites/{slug}', 'contenus', 'actualite'],
    ['GET', '/communaute', 'communautes', 'liste'],
    ['GET', '/communaute/{slug}', 'communautes', 'detail'],
    ['GET|POST', '/communaute/{slug}/integrer', 'communautes', 'integrer'],
    ['POST', '/communaute/{slug}/quitter', 'communautes', 'quitter'],

    // Comptes
    ['GET', '/compte/inscription', 'comptes', 'inscription'],
    ['GET', '/compte/inscription/verification', 'comptes', 'verification'],
    ['POST', '/compte/inscription/renvoyer', 'comptes', 'renvoyer_confirmation'],
    ['GET|POST', '/compte/inscription/{slug}', 'comptes', 'inscription_formulaire'],
    ['GET', '/compte/confirmer/{jeton}', 'comptes', 'confirmer'],
    ['GET|POST', '/compte/connexion', 'comptes', 'connexion'],
    ['POST', '/compte/deconnexion', 'comptes', 'deconnexion'],
    ['GET|POST', '/compte/mot-de-passe-oublie', 'comptes', 'mdp_oublie'],
    ['GET', '/compte/mot-de-passe-oublie/envoye', 'comptes', 'mdp_envoye'],
    ['GET|POST', '/compte/mot-de-passe/{jeton}', 'comptes', 'mdp_nouveau'],
    ['GET', '/compte/mot-de-passe-modifie', 'comptes', 'mdp_modifie'],
    ['GET', '/compte/mon-espace', 'espace', 'tableau_de_bord'],
    ['GET', '/compte/mon-espace/communautes', 'espace', 'mes_communautes'],
    ['GET', '/compte/mon-espace/opportunites', 'espace', 'mes_opportunites'],
    ['GET|POST', '/compte/mon-espace/profil', 'espace', 'mon_profil'],
    ['GET|POST', '/compte/mon-espace/parametres', 'espace', 'parametres_compte'],
    ['GET', '/compte/membres/{id}', 'espace', 'profil_public'],

    // Administration
    ['GET', '/gestion', 'admin', 'tableau_de_bord'],
    ['GET', '/gestion/recherche', 'admin', 'recherche'],
    ['POST', '/gestion/chiffres-rapides', 'admin', 'chiffres_rapides'],
    ['POST', '/gestion/exemples', 'admin', 'exemples'],
    ['GET|POST', '/gestion/mon-compte', 'admin', 'mon_compte'],
    ['POST', '/gestion/messages/{id}/traiter', 'admin', 'message_traiter'],
    ['POST', '/gestion/newsletter/{id}/basculer', 'admin', 'abonne_basculer'],
    ['POST', '/gestion/chiffres/enregistrer', 'admin', 'chiffres_enregistrer'],
    ['GET|POST', '/gestion/demandes/{id}', 'admin', 'demande'],
    ['POST', '/gestion/{slug}/action', 'admin', 'action_groupee'],
    ['GET', '/gestion/{slug}', 'admin', 'liste'],
    ['GET|POST', '/gestion/{slug}/ajouter', 'admin', 'formulaire'],
    ['GET|POST', '/gestion/{slug}/{id}', 'admin', 'formulaire'],
    ['POST', '/gestion/{slug}/{id}/supprimer', 'admin', 'supprimer'],

    // Installation (une seule fois)
    ['GET|POST', '/installation', 'installation', 'installer'],
];

function router(string $chemin, string $methode): void
{
    $chemin_autorise = false;
    foreach (ROUTES as [$methodes, $modele, $fichier, $fonction]) {
        $regex = '#^' . strtr(preg_quote($modele, '#'), [
            '\{slug\}' => '(?P<slug>[a-z0-9][a-z0-9\-]*)',
            '\{id\}' => '(?P<id>\d+)',
            '\{jeton\}' => '(?P<jeton>[a-f0-9]{64})',
        ]) . '$#';
        if (!preg_match($regex, $chemin, $m)) {
            continue;
        }
        $chemin_autorise = true;
        if (!in_array($methode, explode('|', $methodes), true)) {
            continue;
        }
        if ($methode === 'POST') {
            verifier_csrf();
        }
        require_once RACINE . '/app/controleurs/' . $fichier . '.php';
        $arguments = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
        if (isset($arguments['id'])) {
            $arguments['id'] = (int) $arguments['id'];
        }
        $fonction(...$arguments);
        return;
    }
    if ($chemin_autorise) {
        page_erreur(405);
    }
    page_erreur(404);
}

function base_installee(): bool
{
    static $ok = null;
    if ($ok === null) {
        try {
            $ok = (int) valeur("SELECT COUNT(*) FROM utilisateurs WHERE role_admin = 'super'") > 0;
        } catch (PDOException $e) {
            $ok = false;
        }
    }
    return $ok;
}
