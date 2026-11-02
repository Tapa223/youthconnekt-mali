<?php
/*
 * Communautés thématiques : liste, présentation détaillée, demandes pour les rejoindre.
 */

declare(strict_types=1);

function liste(): void
{
    $u = utilisateur();
    $statuts = statuts_du_membre($u);
    $communautes = array_map(function ($c) use ($statuts) {
        $c['statut_membre'] = $statuts[(int) $c['id']] ?? null;
        return $c;
    }, communautes_actives());

    afficher(vue('pages/communautes', [
        'titre' => 'Communauté | YouthConnekt Mali',
        'description' => 'Les communautés thématiques de YouthConnekt Mali : rejoignez celles qui vous ressemblent.',
        'communautes' => $communautes,
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

function detail(string $slug): void
{
    $c = trouver_communaute($slug);
    $u = utilisateur();
    if (!$u) {
        // La présentation détaillée est réservée aux membres connectés (maquette)
        flash('succes', 'Connectez-vous pour découvrir la communauté « ' . $c['nom'] . ' » et demander à l\'intégrer.');
        rediriger(url('/compte/connexion', ['suivant' => $c['url']]));
    }
    $demande = ligne('SELECT * FROM demandes_adhesion WHERE utilisateur_id = ? AND communaute_id = ?', [$u['id'], $c['id']]);

    afficher(vue('pages/communaute', [
        'titre' => $c['nom'] . ' | Communauté | YouthConnekt Mali',
        'c' => $c,
        'statut' => $demande['statut'] ?? null,
        'opportunites' => opportunites_ouvertes('AND id IN (SELECT opportunite_id FROM opportunite_communaute WHERE communaute_id = ?) ORDER BY date_limite IS NULL, date_limite LIMIT 4', [$c['id']]),
        'activites_recentes' => actualites_publiees('AND id IN (SELECT actualite_id FROM actualite_communaute WHERE communaute_id = ?) ORDER BY publie_le DESC LIMIT 4', [$c['id']]),
    ]));
}

function rejoindre(string $slug): void
{
    $u = exiger_connexion();
    $c = trouver_communaute($slug);
    $automatique = (bool) parametres()['acceptation_automatique'];
    $statut = $automatique ? 'acceptee' : 'en_attente';
    $demande = ligne('SELECT * FROM demandes_adhesion WHERE utilisateur_id = ? AND communaute_id = ?', [$u['id'], $c['id']]);
    if (!$demande) {
        inserer('demandes_adhesion', [
            'utilisateur_id' => $u['id'], 'communaute_id' => $c['id'], 'statut' => $statut, 'demandee_le' => maintenant(),
            'traitee_le' => $automatique ? maintenant() : null,
        ]);
    } elseif ($demande['statut'] === 'refusee') {
        // Une demande non retenue peut être renouvelée
        requete('UPDATE demandes_adhesion SET statut = ?, demandee_le = ?, traitee_le = NULL, traitee_par = NULL WHERE id = ?', [$statut, maintenant(), $demande['id']]);
    } else {
        $statut = $demande['statut'];
    }
    if ($statut === 'acceptee') {
        flash('succes', 'Bienvenue dans la communauté ' . $c['nom'] . '.');
    } else {
        flash('succes', 'Votre demande pour intégrer ' . $c['nom'] . ' a bien été envoyée. Vous la suivez dans votre espace.');
    }
    rediriger(adresse_retour($c['url']));
}

function quitter(string $slug): void
{
    $u = exiger_connexion();
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
