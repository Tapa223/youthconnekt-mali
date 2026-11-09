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
    'en_attente' => 'En attente',
    'retenue' => 'Retenue',
    'non_retenue' => 'Non retenue',
];

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
    }
    return $roles;
}

function champs_initiative(int $initiative_id): array
{
    $champs = lignes('SELECT * FROM initiative_champs WHERE initiative_id = ? ORDER BY ordre, id', [$initiative_id]);
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
