<?php
/*
 * Lecture des contenus publiés : paramètres du site, initiatives, opportunités,
 * actualités, communautés... Chaque fonction renvoie des tableaux prêts pour les vues.
 */

declare(strict_types=1);

// Condition « publié » : statut publié et date de publication atteinte
const PUBLIE = "statut = 'publie' AND publie_le <= NOW()";

function parametres(): array
{
    static $p = null;
    if ($p === null) {
        try {
            $p = ligne('SELECT * FROM parametres WHERE id = 1') ?? [];
        } catch (PDOException $e) {
            $p = []; // base pas encore installée
        }
        $p += [
            'accroche_accueil' => '', 'presentation_accueil' => '', 'adresse' => '', 'horaires' => '',
            'telephone' => '', 'email_contact' => '', 'lien_facebook' => '', 'lien_instagram' => '',
            'lien_linkedin' => '', 'lien_x' => '', 'lien_youtube' => '', 'lien_tiktok' => '',
            'photos_provisoires' => 1, 'acceptation_automatique' => 0,
        ];
    }
    return $p;
}

function reseaux_sociaux(): array
{
    $p = parametres();
    $liens = [];
    foreach (['lien_facebook' => 'Facebook', 'lien_instagram' => 'Instagram', 'lien_linkedin' => 'LinkedIn', 'lien_x' => 'X', 'lien_youtube' => 'YouTube', 'lien_tiktok' => 'TikTok'] as $cle => $nom) {
        if (!empty($p[$cle])) {
            $liens[$nom] = $p[$cle];
        }
    }
    return $liens;
}

function photos_provisoires(): bool
{
    return (bool) parametres()['photos_provisoires'];
}

function mot_ministre(): array
{
    return ligne('SELECT * FROM mot_ministre WHERE id = 1') ?? ['nom' => '', 'titre_officiel' => '', 'photo' => '', 'extrait' => '', 'message' => '', 'publie' => 0];
}

function axes(): array
{
    return lignes('SELECT * FROM axes ORDER BY ordre, nom');
}

// ---------------------------------------------------------------------------
// Initiatives
// ---------------------------------------------------------------------------

const SELECT_INITIATIVE = 'SELECT i.*, a.nom AS axe_nom, a.slug AS axe_slug, a.couleur AS axe_couleur
    FROM initiatives i LEFT JOIN axes a ON a.id = i.axe_id';

function initiative_preparer(array $i): array
{
    $i['url'] = url('/initiatives/' . $i['slug']);
    return $i;
}

function initiatives_publiees(string $suite = '', array $params = []): array
{
    $sql = SELECT_INITIATIVE . " WHERE i.statut = 'publie' AND i.publie_le <= NOW() " . $suite;
    return array_map('initiative_preparer', lignes($sql, $params));
}

// ---------------------------------------------------------------------------
// Opportunités
// ---------------------------------------------------------------------------

/** Types d'opportunités (gérés dans l'administration) : [slug => ['nom' => ..., 'couleur' => ...]] */
function types_opportunite(): array
{
    static $types = null;
    if ($types === null) {
        $types = [];
        try {
            foreach (lignes('SELECT slug, nom, couleur FROM types_opportunite ORDER BY ordre, nom') as $t) {
                $types[$t['slug']] = ['nom' => $t['nom'], 'couleur' => $t['couleur']];
            }
        } catch (Throwable $e) {
            journaliser('Types d\'opportunités indisponibles : ' . $e->getMessage());
        }
    }
    return $types;
}

/** [slug => nom] pour les listes de choix */
function options_types_opportunite(): array
{
    return array_map(fn ($t) => $t['nom'], types_opportunite());
}

function opportunite_preparer(array $o): array
{
    $types = types_opportunite();
    $o['url'] = url('/opportunites/' . $o['slug']);
    $o['couleur'] = $types[$o['type']]['couleur'] ?? 'vert';
    $o['type_libelle'] = $types[$o['type']]['nom'] ?? ucfirst(str_replace('-', ' ', $o['type']));
    $o['jours_restants'] = $o['date_limite'] ? jours_restants($o['date_limite']) : null;
    $o['est_expiree'] = $o['date_limite'] !== null && $o['jours_restants'] < 0;
    return $o;
}

/** Condition « ouverte » : publiée et date limite non dépassée (ou sans date limite). */
const OUVERTE = "statut = 'publie' AND publie_le <= NOW() AND (date_limite IS NULL OR date_limite >= CURDATE())";

function opportunites_ouvertes(string $suite = 'ORDER BY date_limite IS NULL, date_limite, publie_le DESC', array $params = []): array
{
    return array_map('opportunite_preparer', lignes('SELECT * FROM opportunites WHERE ' . OUVERTE . ' ' . $suite, $params));
}

// ---------------------------------------------------------------------------
// Actualités et événements
// ---------------------------------------------------------------------------

function actualite_preparer(array $a): array
{
    $a['url'] = url('/actualites/' . $a['slug']);
    $a['date_affichee'] = $a['date_evenement'] ?: substr($a['publie_le'], 0, 10);
    $a['est_a_venir'] = !empty($a['date_evenement']) && $a['date_evenement'] >= aujourdhui();
    $a['categorie_libelle'] = CATEGORIES_ACTUALITE[$a['categorie']] ?? '';
    $mots = str_word_count(strip_tags(($a['chapeau'] ?? '') . ' ' . ($a['contenu'] ?? '')));
    $a['minutes_lecture'] = max(1, (int) round($mots / 200));
    return $a;
}

function actualites_publiees(string $suite = 'ORDER BY publie_le DESC', array $params = []): array
{
    return array_map('actualite_preparer', lignes('SELECT * FROM actualites WHERE ' . PUBLIE . ' ' . $suite, $params));
}

// ---------------------------------------------------------------------------
// Communautés
// ---------------------------------------------------------------------------

function communaute_preparer(array $c): array
{
    $c['url'] = url('/communaute/' . $c['slug']);
    $c['nb_membres'] = (int) ($c['nb_membres'] ?? 0);
    return $c;
}

const SELECT_COMMUNAUTE = "SELECT c.*, (SELECT COUNT(*) FROM demandes_adhesion d WHERE d.communaute_id = c.id AND d.statut = 'acceptee') AS nb_membres FROM communautes c";

function communautes_actives(int $limite = 0): array
{
    $sql = SELECT_COMMUNAUTE . ' WHERE c.active = 1 ORDER BY c.ordre, c.nom' . ($limite ? ' LIMIT ' . $limite : '');
    return array_map('communaute_preparer', lignes($sql));
}

/** {id de communauté: statut de la demande} pour un membre connecté */
function statuts_du_membre(?array $u): array
{
    if (!$u) {
        return [];
    }
    $r = [];
    foreach (lignes('SELECT communaute_id, statut FROM demandes_adhesion WHERE utilisateur_id = ?', [$u['id']]) as $l) {
        $r[(int) $l['communaute_id']] = $l['statut'];
    }
    return $r;
}

/** Nom de la première communauté (parmi celles du membre) qui cible une opportunité. */
function communaute_via(int $opportunite_id, array $communautes_ids): string
{
    if (!$communautes_ids) {
        return '';
    }
    $params = array_merge([$opportunite_id], $communautes_ids);
    return (string) valeur(
        'SELECT c.nom FROM opportunite_communaute oc JOIN communautes c ON c.id = oc.communaute_id
         WHERE oc.opportunite_id = ? AND oc.communaute_id IN (' . marqueurs($communautes_ids) . ') ORDER BY c.ordre LIMIT 1',
        $params
    );
}

// ---------------------------------------------------------------------------
// Pagination simple
// ---------------------------------------------------------------------------

function paginer(int $total, int $par_page): array
{
    $pages = max(1, (int) ceil($total / $par_page));
    $page = max(1, min($pages, (int) ($_GET['page'] ?? 1)));
    return [
        'page' => $page,
        'pages' => $pages,
        'total' => $total,
        'decalage' => ($page - 1) * $par_page,
        'par_page' => $par_page,
    ];
}

/** Adresse de la page courante avec certains paramètres remplacés. */
function avec_parametres(array $changements): string
{
    $params = array_merge($_GET, $changements);
    $params = array_filter($params, fn ($v) => $v !== null && $v !== '');
    $chemin = strtok(adresse_actuelle(), '?');
    return $chemin . ($params ? '?' . http_build_query($params) : '');
}
