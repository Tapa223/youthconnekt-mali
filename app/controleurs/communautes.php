<?php
/*
 * Communautés thématiques : liste, présentation détaillée, formulaire d'intégration.
 * Parcours : liste > « Voir la communauté » > « Intégrer la communauté » > formulaire > demande envoyée.
 */

declare(strict_types=1);

function liste(): void
{
    $u = utilisateur();
    $statuts = statuts_du_membre($u);
    $recherche = mb_substr(trim(get('q')), 0, 100);

    $communautes = array_map(function ($c) use ($statuts) {
        $c['statut_membre'] = $statuts[(int) $c['id']] ?? null;
        return $c;
    }, communautes_actives());

    if ($recherche !== '') {
        $communautes = array_values(array_filter($communautes, fn ($c) =>
            texte_correspond(implode(' ', [$c['nom'], $c['public_vise'], $c['description'], $c['activites'] ?? '', $c['type_nom'] ?? '']), $recherche)));
    }

    afficher(vue('pages/communautes', [
        'titre' => 'Communauté | YouthConnekt Mali',
        'description' => 'Les communautés thématiques de YouthConnekt Mali : rejoignez celles qui vous ressemblent.',
        'communautes' => $communautes,
        'recherche' => $recherche,
    ]));
}

function trouver_communaute(string $slug, bool $actives_seulement = true): array
{
    $c = ligne(SELECT_COMMUNAUTE . ' WHERE c.slug = ?' . ($actives_seulement ? ' AND c.active = 1' : ''), [$slug]);
    if (!$c) {
        page_erreur(404);
    }
    return communaute_preparer($c);
}

function exiger_connexion_communaute(array $c): array
{
    $u = utilisateur();
    if (!$u) {
        // La présentation détaillée est réservée aux membres connectés (cahier des charges, 6.6)
        rediriger(url('/compte/connexion', ['suivant' => $c['url']]));
    }
    return $u;
}

function detail(string $slug): void
{
    $c = trouver_communaute($slug);
    $u = exiger_connexion_communaute($c);
    $demande = est_membre($u) ? ligne('SELECT * FROM demandes_adhesion WHERE utilisateur_id = ? AND communaute_id = ?', [$u['id'], $c['id']]) : null;

    afficher(vue('pages/communaute', [
        'titre' => $c['nom'] . ' | Communauté | YouthConnekt Mali',
        'c' => $c,
        'statut' => $demande['statut'] ?? null,
        'compte_admin' => !est_membre($u),
        'opportunites' => opportunites_ouvertes('AND id IN (SELECT opportunite_id FROM opportunite_communaute WHERE communaute_id = ?) ORDER BY date_limite IS NULL, date_limite LIMIT 4', [$c['id']]),
        'activites_recentes' => actualites_publiees('AND id IN (SELECT actualite_id FROM actualite_communaute WHERE communaute_id = ?) ORDER BY COALESCE(date_evenement, DATE(publie_le)) DESC LIMIT 4', [$c['id']]),
        'membres' => membres_visibles($c),
    ]));
}

/**
 * Membres de la communauté qui ont choisi de rendre leur profil visible (« Visible dans la communauté »).
 * Chacun mène à son profil.
 */
function membres_visibles(array $c): array
{
    $membres = [];
    $lignes = lignes(
        "SELECT u.* FROM utilisateurs u JOIN demandes_adhesion d ON d.utilisateur_id = u.id
         WHERE d.communaute_id = ? AND d.statut = 'acceptee' AND u.actif = 1 ORDER BY d.traitee_le DESC, d.id DESC",
        [$c['id']]
    );
    foreach ($lignes as $l) {
        $m = completer_utilisateur($l);
        $p = $m['profil'];
        if (!$p || !$p['visible']) {
            continue;
        }
        $organisation = $m['type_compte'] === 'organisation';
        $membres[] = [
            'url' => url('/compte/membres/' . $m['id']),
            'nom' => $m['nom_affiche'],
            'initiales' => $m['initiales'],
            'photo' => $organisation ? ($p['logo'] ?? '') : ($p['photo'] ?? ''),
            'detail' => $organisation ? ($p['secteur'] ?: (TYPES_STRUCTURE[$p['type_structure']] ?? '')) : (($p['domaine'] ?? '') !== '' ? $p['domaine'] : (CATEGORIES_INDIVIDUELLES[$p['categorie']] ?? '')),
        ];
    }
    return $membres;
}

/** Formulaire « Intégrer la communauté » : motivation, apport, accord. */
function integrer(string $slug): void
{
    $c = trouver_communaute($slug);
    exiger_connexion_communaute($c);
    $u = exiger_membre();
    $demande = ligne('SELECT * FROM demandes_adhesion WHERE utilisateur_id = ? AND communaute_id = ?', [$u['id'], $c['id']]);
    if ($demande && $demande['statut'] !== 'refusee') {
        flash('succes', $demande['statut'] === 'acceptee' ? 'Vous faites déjà partie de cette communauté.' : 'Votre demande est déjà en cours de traitement.');
        rediriger($c['url']);
    }

    $valeurs = ['motivation' => '', 'projet' => '', 'apport' => '', 'accord' => ''];
    $erreurs = [];
    if (est_post()) {
        foreach (array_keys($valeurs) as $k) {
            $valeurs[$k] = post($k);
        }
        if (mb_strlen($valeurs['motivation']) < 20) {
            $erreurs['motivation'] = $valeurs['motivation'] === '' ? 'Dites en quelques mots ce qui vous amène dans cette communauté.' : 'Développez un peu plus (20 caractères au minimum).';
        } elseif (mb_strlen($valeurs['motivation']) > 1000) {
            $erreurs['motivation'] = '1 000 caractères au maximum.';
        }
        foreach (['apport', 'projet'] as $champ) {
            if (mb_strlen($valeurs[$champ]) > 1000) {
                $erreurs[$champ] = '1 000 caractères au maximum.';
            }
        }
        if ($valeurs['accord'] === '') {
            $erreurs['accord'] = 'Cochez cette case pour envoyer votre demande.';
        }
        if (!$erreurs) {
            $automatique = (bool) parametres()['acceptation_automatique'];
            $donnees = [
                'statut' => $automatique ? 'acceptee' : 'en_attente',
                'motivation' => $valeurs['motivation'],
                'apport' => $valeurs['apport'],
                'projet' => $valeurs['projet'],
                'demandee_le' => maintenant(),
                'traitee_le' => $automatique ? maintenant() : null,
                'traitee_par' => null,
            ];
            if ($demande) {
                mettre_a_jour('demandes_adhesion', $donnees, (int) $demande['id']);
            } else {
                inserer('demandes_adhesion', $donnees + ['utilisateur_id' => $u['id'], 'communaute_id' => $c['id']]);
            }
            flash('succes', $automatique
                ? 'Bienvenue dans la communauté ' . $c['nom'] . '.'
                : 'Votre demande pour intégrer ' . $c['nom'] . " a bien été envoyée. L'équipe répond sous 24 heures ; sans réponse, vous êtes intégré automatiquement.");
            rediriger($c['url']);
        }
    }

    afficher(vue('pages/integrer', [
        'titre' => 'Intégrer ' . $c['nom'] . ' | YouthConnekt Mali',
        'noindex' => true,
        'c' => $c,
        'u' => $u,
        'valeurs' => $valeurs,
        'erreurs' => $erreurs,
        'refusee' => $demande !== null,
    ]));
}

function quitter(string $slug): void
{
    $u = exiger_membre();
    $c = trouver_communaute($slug, false);
    $statut = valeur('SELECT statut FROM demandes_adhesion WHERE utilisateur_id = ? AND communaute_id = ?', [$u['id'], $c['id']]);
    if ($statut === 'acceptee' || $statut === 'en_attente') {
        requete('DELETE FROM demandes_adhesion WHERE utilisateur_id = ? AND communaute_id = ?', [$u['id'], $c['id']]);
        flash('succes', $statut === 'acceptee'
            ? 'Vous ne faites plus partie de la communauté ' . $c['nom'] . '.'
            : 'Votre demande pour intégrer ' . $c['nom'] . ' a été annulée.');
    }
    rediriger(adresse_retour($c['url']));
}
