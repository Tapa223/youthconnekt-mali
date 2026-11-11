<?php
/*
 * Participations aux initiatives : rôles et formulaire créés par l'administrateur,
 * candidatures des membres, décisions de l'équipe.
 */

declare(strict_types=1);

/** Types de champs que l'administrateur peut ajouter au formulaire */
const TYPES_CHAMP_PARTICIPATION = [
    'texte' => 'Réponse courte',
    'zone' => 'Réponse longue',
    'choix' => 'Un choix dans une liste',
    'cases' => 'Plusieurs choix possibles',
    'oui_non' => 'Oui ou non',
    'date' => 'Date',
    'nombre' => 'Nombre',
];

const STATUTS_PARTICIPATION = [
    'inscrit' => 'Inscrit',
    'en_attente' => 'En attente',
    'retenue' => 'Retenue',
    'non_retenue' => 'Non retenue',
];

/** Inscription : validée tout de suite (billet immédiat). Candidature : étudiée par l'équipe. */
const MODES_ROLE = [
    'inscription' => 'Inscription (validée tout de suite)',
    'candidature' => "Candidature (l'équipe choisit)",
];

/** Le billet est valable une fois l'inscription faite ou la candidature retenue. */
function billet_valable(array $p): bool
{
    return in_array($p['statut'] ?? '', ['inscrit', 'retenue'], true);
}

/** Code de billet unique, facile à lire et à saisir (sans 0, O, 1, I). */
function nouveau_code_billet(): string
{
    $lettres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    do {
        $code = 'YCM-';
        for ($k = 0; $k < 5; $k++) {
            $code .= $lettres[random_int(0, strlen($lettres) - 1)];
        }
    } while (valeur('SELECT COUNT(*) FROM participations WHERE code = ?', [$code]));
    return $code;
}

/** Rôles d'une initiative, avec le nombre de personnes déjà proposées (hors non retenues) et les places restantes. */
function roles_initiative(int $initiative_id): array
{
    $roles = lignes(
        "SELECT r.*, (SELECT COUNT(*) FROM participations p WHERE p.role_id = r.id AND p.statut <> 'non_retenue') AS nb
         FROM initiative_roles r WHERE r.initiative_id = ? ORDER BY r.ordre, r.id",
        [$initiative_id]
    );
    foreach ($roles as &$r) {
        $r['restantes'] = $r['places'] === null ? null : max(0, (int) $r['places'] - (int) $r['nb']);
        $r['complet'] = $r['restantes'] === 0;
        $r['champs'] = champs_role((int) $r['id']);
    }
    return $roles;
}

/** Questions d'un rôle (chaque rôle a les siennes). */
function champs_role(int $role_id): array
{
    $champs = lignes('SELECT * FROM initiative_champs WHERE role_id = ? ORDER BY ordre, id', [$role_id]);
    foreach ($champs as &$c) {
        $c['choix'] = lignes_texte((string) $c['options']);
    }
    return $champs;
}

/** Toutes les questions d'une initiative, rôle par rôle (pour l'export et le tableau). */
function champs_initiative(int $initiative_id): array
{
    $champs = lignes('SELECT c.*, r.nom AS role_nom FROM initiative_champs c JOIN initiative_roles r ON r.id = c.role_id WHERE c.initiative_id = ? ORDER BY r.ordre, r.id, c.ordre, c.id', [$initiative_id]);
    foreach ($champs as &$c) {
        $c['choix'] = lignes_texte((string) $c['options']);
    }
    return $champs;
}

/** Les participations sont-elles ouvertes ? (case cochée, initiative pas terminée, date limite non dépassée, au moins un rôle) */
function participations_ouvertes(array $i, ?array $roles = null): bool
{
    if (empty($i['participations_ouvertes']) || ($i['etat'] ?? '') === 'terminee') {
        return false;
    }
    if (!empty($i['participations_fin']) && $i['participations_fin'] < date('Y-m-d')) {
        return false;
    }
    $roles ??= roles_initiative((int) $i['id']);
    return (bool) array_filter($roles, fn ($r) => !$r['complet']);
}

/**
 * Lit et vérifie les réponses du formulaire. Renvoie [réponses à enregistrer, erreurs].
 * Chaque réponse garde le libellé de la question : la modifier ensuite ne change pas les réponses déjà reçues.
 */
function lire_reponses(array $champs): array
{
    $reponses = [];
    $erreurs = [];
    foreach ($champs as $c) {
        $nom = 'champ_' . $c['id'];
        $brut = $_POST[$nom] ?? '';
        if ($c['type'] === 'cases') {
            $valeur = array_values(array_intersect(array_map('strval', (array) $brut), $c['choix']));
            $texte = implode(', ', $valeur);
        } else {
            $valeur = trim(is_string($brut) ? $brut : '');
            if ($c['type'] === 'choix' && $valeur !== '' && !in_array($valeur, $c['choix'], true)) {
                $valeur = '';
            }
            if ($c['type'] === 'oui_non' && !in_array($valeur, ['Oui', 'Non'], true)) {
                $valeur = '';
            }
            if ($c['type'] === 'nombre' && $valeur !== '' && !is_numeric(str_replace(',', '.', $valeur))) {
                $erreurs[$nom] = 'Indiquez un nombre.';
            }
            if ($c['type'] === 'date' && $valeur !== '' && !DateTimeImmutable::createFromFormat('!Y-m-d', $valeur)) {
                $erreurs[$nom] = "Cette date n'est pas valide.";
            }
            $valeur = mb_substr($valeur, 0, $c['type'] === 'zone' ? 3000 : 300);
            $texte = $c['type'] === 'date' && $valeur !== '' ? date_longue($valeur) : $valeur;
        }
        if ($c['requis'] && ($texte === '' || $texte === [])) {
            $erreurs[$nom] = 'Cette réponse est demandée.';
        }
        $reponses[] = ['id' => (int) $c['id'], 'question' => $c['libelle'], 'reponse' => $texte, 'brut' => $valeur];
    }
    return [$reponses, $erreurs];
}

/** Réponses enregistrées d'une participation : [['question' => …, 'reponse' => …], …] */
function reponses_participation(array $p): array
{
    return json_decode((string) ($p['reponses'] ?? ''), true) ?: [];
}

/** Personnes inscrites ou retenues : leur nombre et quelques visages (photo ou initiales) pour la page de l'initiative. */
function inscrits_initiative(int $initiative_id): array
{
    $liste = lignes(
        "SELECT COALESCE(pi.photo, po.logo) AS photo, COALESCE(pi.prenom, po.nom_structure, '') AS prenom, COALESCE(pi.nom, '') AS nom
         FROM participations p LEFT JOIN profils_individuels pi ON pi.utilisateur_id = p.utilisateur_id LEFT JOIN profils_organisations po ON po.utilisateur_id = p.utilisateur_id
         WHERE p.initiative_id = ? AND p.statut IN ('inscrit', 'retenue') ORDER BY (COALESCE(pi.photo, po.logo) IS NULL), p.cree_le DESC LIMIT 6",
        [$initiative_id]
    );
    return [
        'nombre' => (int) valeur("SELECT COUNT(*) FROM participations WHERE initiative_id = ? AND statut IN ('inscrit', 'retenue')", [$initiative_id]),
        'visages' => array_map(fn ($l) => ['photo' => $l['photo'], 'initiales' => mb_strtoupper(mb_substr($l['prenom'], 0, 1) . mb_substr($l['nom'], 0, 1)) ?: 'YC'], $liste),
    ];
}
