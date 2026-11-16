<?php
/*
 * Organisations du réseau : l'annuaire public des entreprises, startups, associations et ONG inscrites,
 * et la fiche publique de chacune (seulement celles qui ont choisi d'apparaître dans l'annuaire).
 */

const ORGANISATIONS_PUBLIQUES = "FROM profils_organisations p JOIN utilisateurs u ON u.id = p.utilisateur_id
    WHERE p.dans_annuaire = 1 AND u.actif = 1 AND u.type_compte = 'organisation'";

function annuaire(): void
{
    $type = get('type');
    if (!isset(TYPES_STRUCTURE[$type])) {
        $type = '';
    }
    $region = get('region');
    if (!isset(REGIONS[$region])) {
        $region = '';
    }
    $sql = 'SELECT p.* ' . ORGANISATIONS_PUBLIQUES;
    $params = [];
    if ($type) { $sql .= ' AND p.type_structure = ?'; $params[] = $type; }
    if ($region) { $sql .= ' AND p.region = ?'; $params[] = $region; }
    try {
        $organisations = lignes($sql . ' ORDER BY p.partenaire_chapitre DESC, p.verifiee DESC, p.nom_structure', $params);
        $total = (int) valeur('SELECT COUNT(*) ' . ORGANISATIONS_PUBLIQUES);
    } catch (Throwable $e) {
        $organisations = []; // mise à jour 27 pas encore importée
        $total = 0;
    }
    afficher(vue('pages/organisations', [
        'titre' => 'Organisations du réseau | YouthConnekt Mali',
        'description' => 'Entreprises, startups, associations et ONG engagées pour la jeunesse malienne au sein de YouthConnekt Mali.',
        'organisations' => $organisations,
        'total' => $total,
        'type' => $type,
        'region' => $region,
    ]));
}

function fiche(int $id): void
{
    try {
        $o = ligne('SELECT p.*, u.inscrit_le ' . ORGANISATIONS_PUBLIQUES . ' AND p.id = ?', [$id]);
    } catch (Throwable $e) {
        $o = null;
    }
    if (!$o) {
        page_erreur(404);
    }
    $opportunites = array_map('opportunite_preparer', lignes(
        'SELECT * FROM opportunites WHERE ' . PUBLIE . ' AND propose_par = ? AND (date_limite IS NULL OR date_limite >= CURDATE()) ORDER BY publie_le DESC LIMIT 6',
        [$o['utilisateur_id']]
    ));
    afficher(vue('pages/organisation', [
        'titre' => $o['nom_structure'] . ' | Organisations du réseau | YouthConnekt Mali',
        'description' => mb_substr(trim((string) $o['presentation']) ?: $o['nom_structure'] . ', membre du réseau YouthConnekt Mali.', 0, 160),
        'image_partage' => $o['logo'],
        'o' => $o,
        'liens' => liens_profil((int) $o['utilisateur_id']),
        'opportunites' => $opportunites,
    ]));
}

/** Organisation qui a proposé une opportunité, si elle apparaît dans l'annuaire (pour afficher son logo et sa fiche). */
function organisation_de(?int $utilisateur_id): ?array
{
    if (!$utilisateur_id) {
        return null;
    }
    try {
        return ligne('SELECT p.* ' . ORGANISATIONS_PUBLIQUES . ' AND p.utilisateur_id = ?', [$utilisateur_id]) ?: null;
    } catch (Throwable $e) {
        return null;
    }
}
