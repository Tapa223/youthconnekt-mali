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
    $types = types_communaute();
    $type_actif = get('type');
    if (!isset($types[$type_actif])) {
        $type_actif = '';
    }
    $recherche = mb_substr(trim(get('q')), 0, 100);

    $toutes = array_map(function ($c) use ($statuts) {
        $c['statut_membre'] = $statuts[(int) $c['id']] ?? null;
        return $c;
    }, communautes_actives());

    // Nombre de communautés par type, pour le menu (seuls les types utilisés y figurent)
    $nombres = [];
    foreach ($toutes as $c) {
        if (isset($types[$c['type']])) {
            $nombres[$c['type']] = ($nombres[$c['type']] ?? 0) + 1;
        }
    }

    $communautes = array_values(array_filter($toutes, function ($c) use ($type_actif, $recherche) {
        if ($type_actif !== '' && $c['type'] !== $type_actif) {
            return false;
        }
        if ($recherche !== '') {
            $texte = mb_strtolower($c['nom'] . ' ' . $c['public_vise'] . ' ' . $c['description'] . ' ' . $c['type_libelle']);
            return mb_strpos($texte, mb_strtolower($recherche)) !== false;
        }
        return true;
    }));

    afficher(vue('pages/communautes', [
        'titre' => 'Communauté | YouthConnekt Mali',
        'description' => 'Les communautés thématiques de YouthConnekt Mali : rejoignez celles qui vous ressemblent.',
        'communautes' => $communautes,
        'total' => count($toutes),
        'types' => $types,
        'type_actif' => $type_actif,
        'nombres' => $nombres,
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
        'activites_recentes' => actualites_publiees('AND id IN (SELECT actualite_id FROM actualite_communaute WHERE communaute_id = ?) ORDER BY publie_le DESC LIMIT 4', [$c['id']]),
    ]));
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

    $valeurs = ['motivation' => '', 'apport' => '', 'accord' => ''];
    $erreurs = [];
    if (est_post()) {
        foreach (array_keys($valeurs) as $k) {
            $valeurs[$k] = post($k);
        }
        if (mb_strlen($valeurs['motivation']) < 20) {
            $erreurs['motivation'] = $valeurs['motivation'] === '' ? 'Dites en quelques mots pourquoi vous souhaitez intégrer cette communauté.' : 'Développez un peu plus (20 caractères au minimum).';
        } elseif (mb_strlen($valeurs['motivation']) > 1000) {
            $erreurs['motivation'] = '1 000 caractères au maximum.';
        }
        if (mb_strlen($valeurs['apport']) > 1000) {
            $erreurs['apport'] = '1 000 caractères au maximum.';
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
                : 'Votre demande pour intégrer ' . $c['nom'] . ' a bien été envoyée. Vous la suivez dans votre espace.');
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
