<?php
/*
 * Noyau du site : configuration, base de données, sécurité et petits outils
 * utilisés partout (adresses, dates en français, mise en forme des textes...).
 */

declare(strict_types=1);

define('RACINE', dirname(__DIR__));

require RACINE . '/app/listes.php';

date_default_timezone_set('Africa/Bamako');
mb_internal_encoding('UTF-8');

// ---------------------------------------------------------------------------
// Configuration
// ---------------------------------------------------------------------------

function config(string $cle, $defaut = null)
{
    static $config = null;
    if ($config === null) {
        $fichier = RACINE . '/config.php';
        if (!is_file($fichier)) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
            echo '<!doctype html><meta charset="utf-8"><title>Configuration manquante</title>'
                . '<body style="font-family:system-ui;max-width:640px;margin:10vh auto;padding:0 20px;color:#17211f">'
                . '<h1 style="color:#035E4D">Configuration manquante</h1>'
                . '<p>Copiez le fichier <code>config.exemple.php</code> sous le nom <code>config.php</code>, '
                . 'dans le même dossier, puis indiquez-y les informations de la base de données.</p>';
            exit;
        }
        $config = require $fichier;
    }
    $valeur = $config;
    foreach (explode('.', $cle) as $morceau) {
        if (!is_array($valeur) || !array_key_exists($morceau, $valeur)) {
            return $defaut;
        }
        $valeur = $valeur[$morceau];
    }
    return $valeur;
}

function en_developpement(): bool
{
    return (bool) config('developpement', false);
}

// ---------------------------------------------------------------------------
// Erreurs
// ---------------------------------------------------------------------------

function journaliser(string $fichier, string $texte): void
{
    $dossier = RACINE . '/stockage';
    if (!is_dir($dossier)) {
        @mkdir($dossier, 0775, true);
    }
    @file_put_contents($dossier . '/' . $fichier, '[' . date('Y-m-d H:i:s') . '] ' . $texte . "\n", FILE_APPEND | LOCK_EX);
}

function installer_gestion_erreurs(): void
{
    error_reporting(E_ALL);
    ini_set('display_errors', en_developpement() ? '1' : '0');
    set_exception_handler(function (Throwable $e) {
        journaliser('erreurs.log', get_class($e) . ' : ' . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")\n" . $e->getTraceAsString());
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }
        if (en_developpement()) {
            echo '<pre style="white-space:pre-wrap;padding:20px;font:14px/1.5 monospace;background:#fde8e0;color:#7C0D02">'
                . e(get_class($e) . ' : ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString())
                . '</pre>';
        } else {
            $fichier = RACINE . '/vues/pages/erreur.php';
            $code = 500;
            $titre_erreur = 'Un problème est survenu';
            $texte_erreur = "La page n'a pas pu s'afficher. Réessayez dans quelques instants.";
            if (is_file($fichier)) {
                try {
                    echo vue('pages/erreur', compact('code', 'titre_erreur', 'texte_erreur'));
                    return;
                } catch (Throwable $ignore) {
                }
            }
            echo 'Un problème est survenu.';
        }
    });
}

// ---------------------------------------------------------------------------
// Base de données
// ---------------------------------------------------------------------------

function bdd(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $b = config('bdd');
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $b['hote'], (int) ($b['port'] ?? 3306), $b['nom']);
        $pdo = new PDO($dsn, $b['utilisateur'], $b['mot_de_passe'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->exec("SET time_zone = '+00:00'");
    }
    return $pdo;
}

function requete(string $sql, array $parametres = []): PDOStatement
{
    $stmt = bdd()->prepare($sql);
    $stmt->execute($parametres);
    return $stmt;
}

function ligne(string $sql, array $parametres = []): ?array
{
    $r = requete($sql, $parametres)->fetch();
    return $r === false ? null : $r;
}

function lignes(string $sql, array $parametres = []): array
{
    return requete($sql, $parametres)->fetchAll();
}

function valeur(string $sql, array $parametres = [])
{
    $r = requete($sql, $parametres)->fetchColumn();
    return $r === false ? null : $r;
}

function inserer(string $table, array $donnees): int
{
    $colonnes = array_keys($donnees);
    $sql = 'INSERT INTO `' . $table . '` (`' . implode('`,`', $colonnes) . '`) VALUES ('
        . implode(',', array_map(fn ($c) => ':' . $c, $colonnes)) . ')';
    requete($sql, $donnees);
    return (int) bdd()->lastInsertId();
}

function mettre_a_jour(string $table, array $donnees, int $id): void
{
    if (!$donnees) {
        return;
    }
    $sets = implode(',', array_map(fn ($c) => '`' . $c . '` = :' . $c, array_keys($donnees)));
    $donnees['__id'] = $id;
    requete('UPDATE `' . $table . '` SET ' . $sets . ' WHERE id = :__id', $donnees);
}

/** Construit « IN (?, ?, ?) » pour une liste d'identifiants. */
function marqueurs(array $valeurs): string
{
    return implode(',', array_fill(0, max(1, count($valeurs)), '?'));
}

// ---------------------------------------------------------------------------
// Adresses
// ---------------------------------------------------------------------------

/** Dossier du site dans l'adresse (« /youthconnekt-mali » sur WAMP, « » en ligne). */
function base_url(): string
{
    static $base = null;
    if ($base === null) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $base = rtrim(str_replace('\\', '/', dirname($script)), '/');
        if ($base === '.') {
            $base = '';
        }
    }
    return $base;
}

function url(string $chemin = '/', array $parametres = []): string
{
    $chemin = '/' . ltrim($chemin, '/');
    $adresse = base_url() . $chemin;
    $parametres = array_filter($parametres, fn ($v) => $v !== null && $v !== '');
    if ($parametres) {
        $adresse .= '?' . http_build_query($parametres);
    }
    return $adresse;
}

function url_absolue(string $chemin = '/'): string
{
    $site = rtrim((string) config('url_site', ''), '/');
    if ($site === '') {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        $site = ($https ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . base_url();
        return $site . '/' . ltrim($chemin, '/');
    }
    // Le chemin reçu peut déjà contenir le dossier du site
    $base = base_url();
    if ($base !== '' && strpos($chemin, $base . '/') === 0) {
        $chemin = substr($chemin, strlen($base));
    }
    return $site . '/' . ltrim($chemin, '/');
}

/** Fichier de assets/ avec un numéro de version pour que les navigateurs prennent la dernière. */
function asset(string $chemin): string
{
    $fichier = RACINE . '/assets/' . ltrim($chemin, '/');
    $version = is_file($fichier) ? '?v=' . filemtime($fichier) : '';
    return base_url() . '/assets/' . ltrim($chemin, '/') . $version;
}

/** Image envoyée depuis l'administration (dossier uploads/). */
function media(?string $chemin): string
{
    return $chemin ? base_url() . '/' . ltrim($chemin, '/') : '';
}

/** Chemin demandé, sans le dossier du site ni les paramètres (« /opportunites/stage »). */
function chemin_actuel(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $uri = rawurldecode($uri);
    $base = base_url();
    if ($base !== '' && strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }
    if (substr($uri, 0, 10) === '/index.php') {
        $uri = substr($uri, 10);
    }
    $uri = '/' . trim($uri, '/');
    return $uri;
}

/** Adresse complète de la page actuelle (dossier du site compris, avec les paramètres). */
function adresse_actuelle(): string
{
    return $_SERVER['REQUEST_URI'] ?? url('/');
}

function rediriger(string $adresse): never
{
    header('Location: ' . $adresse, true, 303);
    exit;
}

/** Adresse de retour fournie par un formulaire, uniquement si elle reste sur ce site. */
function adresse_retour(string $defaut, string $champ = 'suivant'): string
{
    $adresse = (string) ($_POST[$champ] ?? $_GET[$champ] ?? '');
    if ($adresse !== '' && $adresse[0] === '/' && substr($adresse, 0, 2) !== '//' && strpos($adresse, '\\') === false) {
        return $adresse;
    }
    return $defaut;
}

// ---------------------------------------------------------------------------
// Requête
// ---------------------------------------------------------------------------

function est_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function post(string $cle, string $defaut = ''): string
{
    $v = $_POST[$cle] ?? $defaut;
    return is_string($v) ? trim($v) : $defaut;
}

function get(string $cle, string $defaut = ''): string
{
    $v = $_GET[$cle] ?? $defaut;
    return is_string($v) ? trim($v) : $defaut;
}

function adresse_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

// ---------------------------------------------------------------------------
// Session, messages, protection des formulaires
// ---------------------------------------------------------------------------

function demarrer_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('ycm_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => (base_url() ?: '') . '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    // Les sessions « Se souvenir de moi » durent 30 jours
    ini_set('session.gc_maxlifetime', (string) (30 * 86400));
    session_start();
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'texte' => $message];
}

function flashs(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function csrf_jeton(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_jeton()) . '">';
}

function verifier_csrf(): void
{
    $recu = (string) ($_POST['_csrf'] ?? '');
    if ($recu === '' || !hash_equals(csrf_jeton(), $recu)) {
        http_response_code(400);
        flash('erreur', 'Le formulaire a expiré. Merci de recommencer.');
        $retour = $_SERVER['HTTP_REFERER'] ?? url('/');
        $hote = parse_url($retour, PHP_URL_HOST);
        if ($hote && $hote !== ($_SERVER['HTTP_HOST'] ?? '')) {
            $retour = url('/');
        }
        rediriger($retour);
    }
}

// ---------------------------------------------------------------------------
// Vues
// ---------------------------------------------------------------------------

function e($texte): string
{
    return htmlspecialchars((string) ($texte ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Affiche une vue (dossier vues/) dans un gabarit (« site », « auth », « admin » ou null).
 * Les variables de $donnees sont disponibles dans la vue et dans le gabarit.
 */
function vue(string $nom, array $donnees = [], ?string $gabarit = 'site'): string
{
    $contenu = rendre($nom, $donnees);
    if ($gabarit === null) {
        return $contenu;
    }
    return rendre('gabarits/' . $gabarit, $donnees + ['contenu' => $contenu]);
}

function rendre(string $__nom, array $__donnees = []): string
{
    extract($__donnees, EXTR_SKIP);
    ob_start();
    try {
        require RACINE . '/vues/' . $__nom . '.php';
    } catch (Throwable $e) {
        ob_end_clean();
        throw $e;
    }
    return (string) ob_get_clean();
}

/** Inclut un petit morceau de vue avec ses propres variables. */
function partiel(string $nom, array $donnees = []): void
{
    echo rendre($nom, $donnees);
}

function afficher(string $html): never
{
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }
    echo $html;
    exit;
}

function page_erreur(int $code, string $titre = '', string $texte = ''): never
{
    http_response_code($code);
    $titres = [403 => 'Accès réservé', 404 => 'Page introuvable', 400 => 'Demande incorrecte', 405 => 'Action non autorisée'];
    $textes = [
        403 => "Cette page est réservée. Connectez-vous avec un compte autorisé.",
        404 => "La page que vous cherchez n'existe pas ou n'est plus en ligne.",
        400 => "La demande n'a pas pu être traitée.",
        405 => "Cette action n'est pas possible de cette manière.",
    ];
    afficher(vue('pages/erreur', [
        'code' => $code,
        'titre' => ($titre ?: ($titres[$code] ?? 'Erreur')) . ' | YouthConnekt Mali',
        'titre_erreur' => $titre ?: ($titres[$code] ?? 'Erreur'),
        'texte_erreur' => $texte ?: ($textes[$code] ?? ''),
    ]));
}

// ---------------------------------------------------------------------------
// Dates en français
// ---------------------------------------------------------------------------

const MOIS = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
const MOIS_COURTS = ['', 'janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
const MOIS_PASTILLE = ['', 'janv', 'févr', 'mars', 'avr', 'mai', 'juin', 'juil', 'août', 'sept', 'oct', 'nov', 'déc'];

function maintenant(): string
{
    return date('Y-m-d H:i:s');
}

function aujourdhui(): string
{
    return date('Y-m-d');
}

function horodatage(?string $date): ?int
{
    if (!$date) {
        return null;
    }
    $t = strtotime($date);
    return $t === false ? null : $t;
}

/** 12 novembre 2026 */
function date_longue(?string $date): string
{
    $t = horodatage($date);
    return $t ? date('j', $t) . ' ' . MOIS[(int) date('n', $t)] . ' ' . date('Y', $t) : '';
}

/** novembre 2026 */
function mois_annee(?string $date): string
{
    $t = horodatage($date);
    return $t ? MOIS[(int) date('n', $t)] . ' ' . date('Y', $t) : '';
}

/** 12 nov. 2026 */
function date_courte(?string $date): string
{
    $t = horodatage($date);
    return $t ? date('j', $t) . ' ' . MOIS_COURTS[(int) date('n', $t)] . ' ' . date('Y', $t) : '';
}

/** 12 nov. */
function jour_mois(?string $date): string
{
    $t = horodatage($date);
    return $t ? date('j', $t) . ' ' . MOIS_COURTS[(int) date('n', $t)] : '';
}

/** 12/11/2026 */
function date_chiffres(?string $date): string
{
    $t = horodatage($date);
    return $t ? date('d/m/Y', $t) : '—';
}

function jour(?string $date): string
{
    $t = horodatage($date);
    return $t ? date('j', $t) : '';
}

/** Mois en abrégé pour les pastilles de date (« nov ») */
function mois_pastille(?string $date): string
{
    $t = horodatage($date);
    return $t ? MOIS_PASTILLE[(int) date('n', $t)] : '';
}

function date_iso(?string $date): string
{
    $t = horodatage($date);
    return $t ? date('Y-m-d', $t) : '';
}

/** Nombre de jours entre aujourd'hui et une date (négatif si elle est passée). */
function jours_restants(?string $date): ?int
{
    if (!$date) {
        return null;
    }
    $cible = new DateTimeImmutable(substr($date, 0, 10));
    $jour = new DateTimeImmutable(aujourdhui());
    return (int) $jour->diff($cible)->format('%r%a');
}

function libelle_jours(?int $n): string
{
    if ($n === null) {
        return '';
    }
    if ($n <= 0) {
        return 'Dernier jour';
    }
    return 'Plus que ' . $n . ' jour' . ($n > 1 ? 's' : '');
}

/** « il y a 3 jours », pour la boîte de réception */
function il_y_a(?string $date): string
{
    $t = horodatage($date);
    if (!$t) {
        return '';
    }
    $s = max(0, time() - $t);
    if ($s < 60) {
        return "à l'instant";
    }
    $unites = [[86400 * 30, 'mois', 'mois'], [86400, 'jour', 'jours'], [3600, 'heure', 'heures'], [60, 'minute', 'minutes']];
    foreach ($unites as [$duree, $un, $plusieurs]) {
        if ($s >= $duree) {
            $n = intdiv($s, $duree);
            return 'il y a ' . $n . ' ' . ($n > 1 ? $plusieurs : $un);
        }
    }
    return '';
}

// ---------------------------------------------------------------------------
// Textes
// ---------------------------------------------------------------------------

function pluriel(int $n, string $marque = 's'): string
{
    return $n > 1 ? $marque : '';
}

function majuscule_initiale(string $texte): string
{
    return mb_strtoupper(mb_substr($texte, 0, 1)) . mb_substr($texte, 1);
}

function slugifier(string $texte): string
{
    $texte = trim($texte);
    if (function_exists('transliterator_transliterate')) {
        $texte = (string) transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $texte);
    } else {
        $texte = strtolower((string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texte));
    }
    $texte = preg_replace('/[^a-z0-9]+/', '-', $texte);
    return trim((string) $texte, '-');
}

/** Adresse lisible et unique dans une table, à partir d'un titre. */
function slug_unique(string $table, string $texte, ?int $id = null, string $champ = 'slug'): string
{
    $base = substr(slugifier($texte), 0, 170) ?: 'element';
    $slug = $base;
    $n = 2;
    while ((int) valeur('SELECT COUNT(*) FROM `' . $table . '` WHERE `' . $champ . '` = ? AND id <> ?', [$slug, $id ?? 0]) > 0) {
        $slug = $base . '-' . $n++;
    }
    return $slug;
}

/** Une idée par ligne : découpe un texte en lignes non vides. */
function lignes_texte(?string $texte): array
{
    $resultat = [];
    foreach (preg_split('/\R/u', (string) $texte) as $l) {
        $l = trim(ltrim(trim($l), "-•"));
        if ($l !== '') {
            $resultat[] = $l;
        }
    }
    return $resultat;
}

/** Chaque ligne « a | b | c » découpée en $n colonnes. */
function lignes_colonnes(?string $texte, int $n): array
{
    $resultat = [];
    foreach (lignes_texte($texte) as $l) {
        $morceaux = array_map('trim', explode('|', $l));
        $morceaux = array_pad($morceaux, $n, '');
        $resultat[] = array_slice($morceaux, 0, $n);
    }
    return $resultat;
}

/**
 * Texte saisi dans l'administration, découpé en blocs sans qu'on ait à taper de code :
 *  - chaque retour à la ligne commence un nouveau paragraphe (un texte collé depuis Word ou WhatsApp garde les siens) ;
 *  - les lignes qui commencent par un tiret, une puce ou un numéro (1. 2) …) forment une liste ;
 *  - une ligne courte, sans point final, suivie d'un paragraphe, devient un intertitre ;
 *  - une ligne entre guillemets « … » devient une citation, et « — Nom » sur la ligne suivante son auteur.
 * Les anciennes marques (« ## », « > », « [photos] ») restent comprises pour les textes déjà saisis.
 * Retourne une liste de blocs : ['p', texte] | ['h2', texte] | ['ul'|'ol', [éléments]] | ['citation', texte, auteur] | ['photos'].
 */
function blocs_texte(?string $texte): array
{
    $lignes = array_map('trim', explode("\n", str_replace(["\r\n", "\r"], "\n", (string) $texte)));
    $n = count($lignes);
    $suivante = function (int $i) use ($lignes, $n): string {
        for ($j = $i + 1; $j < $n; $j++) {
            if ($lignes[$j] !== '') {
                return $lignes[$j];
            }
        }
        return '';
    };
    $puce = '/^(?:[-•*–·▪●]\s+|(\d{1,2})[.)]\s+)/u';
    $blocs = [];
    for ($i = 0; $i < $n; $i++) {
        $l = $lignes[$i];
        if ($l === '') {
            continue;
        }
        if (mb_strtolower($l) === '[photos]') {
            $blocs[] = ['photos'];
            continue;
        }
        if (str_starts_with($l, '## ')) {
            $blocs[] = ['h2', trim(substr($l, 3))];
            continue;
        }
        if (preg_match($puce, $l, $m)) {
            $type = isset($m[1]) && $m[1] !== '' ? 'ol' : 'ul';
            $element = trim(preg_replace($puce, '', $l));
            $dernier = count($blocs) - 1;
            if ($dernier >= 0 && $blocs[$dernier][0] === $type) {
                $blocs[$dernier][1][] = $element;
            } else {
                $blocs[] = [$type, [$element]];
            }
            continue;
        }
        // Auteur d'une citation : « — Nom » juste après
        if (preg_match('/^[—―]\s*(.+)$/u', $l, $m) && $blocs && end($blocs)[0] === 'citation' && end($blocs)[2] === '') {
            $blocs[count($blocs) - 1][2] = trim($m[1]);
            continue;
        }
        if (str_starts_with($l, '>')) {
            $blocs[] = ['citation', trim(ltrim($l, '> ')), ''];
            continue;
        }
        if (mb_strlen($l) > 30 && preg_match('/^[«"“].+[»"”]$/su', $l)) {
            $blocs[] = ['citation', trim(preg_replace('/^[«"“]\s*|\s*[»"”]$/u', '', $l)), ''];
            continue;
        }
        // Intertitre : ligne courte, sans ponctuation finale, suivie d'un vrai paragraphe
        $apres = $suivante($i);
        if (mb_strlen($l) <= 70 && count(preg_split('/\s+/u', $l)) <= 10 && !preg_match('/[.!?;:,…»"”)\]]$/u', $l)
            && $apres !== '' && mb_strlen($apres) > 70 && !preg_match($puce, $apres) && preg_match('/^\p{Lu}|^\d/u', $l)) {
            $blocs[] = ['h2', $l];
            continue;
        }
        $blocs[] = ['p', $l];
    }
    return $blocs;
}

/** Mise en page sûre d'un texte de l'administration (voir blocs_texte). $galerie : HTML placé à « [photos] » ou à la fin. */
function corps_article(?string $texte, string $galerie = ''): string
{
    $html = '';
    $galerie_placee = false;
    foreach (blocs_texte($texte) as $b) {
        switch ($b[0]) {
            case 'p': $html .= '<p>' . e($b[1]) . '</p>'; break;
            case 'h2': $html .= '<h2>' . e($b[1]) . '</h2>'; break;
            case 'ul':
            case 'ol': $html .= '<' . $b[0] . '>' . implode('', array_map(fn ($x) => '<li>' . e($x) . '</li>', $b[1])) . '</' . $b[0] . '>'; break;
            case 'citation': $html .= '<blockquote><p>' . e($b[1]) . '</p>' . ($b[2] !== '' ? '<footer>' . e($b[2]) . '</footer>' : '') . '</blockquote>'; break;
            case 'photos':
                if ($galerie !== '' && !$galerie_placee) {
                    $html .= $galerie;
                    $galerie_placee = true;
                }
                break;
        }
    }
    if ($galerie !== '' && !$galerie_placee) {
        $html .= $galerie;
    }
    return $html;
}

/** Texte de l'administration mis en paragraphes, listes, intertitres (même règles que les articles). */
function paragraphes(?string $texte): string
{
    return corps_article($texte);
}

function initiales(string $texte): string
{
    $texte = str_replace(['@', '.'], ' ', $texte);
    $mots = array_values(array_filter(preg_split('/\s+/u', trim($texte))));
    if (!$mots) {
        return '?';
    }
    if (count($mots) === 1) {
        return mb_strtoupper(mb_substr($mots[0], 0, 2));
    }
    return mb_strtoupper(mb_substr($mots[0], 0, 1) . mb_substr($mots[1], 0, 1));
}

function couper(string $texte, int $longueur): string
{
    return mb_strlen($texte) > $longueur ? rtrim(mb_substr($texte, 0, $longueur - 1)) . '…' : $texte;
}

// ---------------------------------------------------------------------------
// Couleurs de la charte (classes Tailwind)
// ---------------------------------------------------------------------------

const CLASSES_COULEURS = [
    // couleur : [fond plein + texte, bordure, texte coloré]
    'orange' => ['bg-orange text-white', 'border-orange', 'text-orange'],
    'turquoise' => ['bg-turquoise text-encre', 'border-turquoise', 'text-turquoise'],
    'bleu' => ['bg-bleu text-white', 'border-bleu', 'text-bleu'],
    'vert' => ['bg-vert text-white', 'border-vert', 'text-vert'],
    'rouge' => ['bg-rouge text-white', 'border-rouge', 'text-rouge'],
];

function fond(?string $couleur): string
{
    return (CLASSES_COULEURS[$couleur ?? ''] ?? CLASSES_COULEURS['vert'])[0];
}

function bordure(?string $couleur): string
{
    return (CLASSES_COULEURS[$couleur ?? ''] ?? CLASSES_COULEURS['vert'])[1];
}

function texte_couleur(?string $couleur): string
{
    return (CLASSES_COULEURS[$couleur ?? ''] ?? CLASSES_COULEURS['vert'])[2];
}

/** Valeur qui tourne dans une liste (équivalent de « cycle ») */
function cycle(array $valeurs, int $index): string
{
    return $valeurs[$index % count($valeurs)];
}

// ---------------------------------------------------------------------------
// Images envoyées
// ---------------------------------------------------------------------------

const TAILLE_MAX_IMAGE = 3 * 1024 * 1024;

/**
 * Enregistre une image envoyée dans uploads/<dossier>/ et renvoie son chemin.
 * L'image est vérifiée (JPG, PNG ou WebP, 3 Mo maximum), réduite si elle est très grande
 * et réenregistrée, ce qui retire les informations cachées (position GPS...).
 * Renvoie null si aucun fichier n'a été choisi ; lève une RuntimeException en cas de problème.
 */
function televerser_image(?array $fichier, string $dossier, int $largeur_max = 1800): ?string
{
    if (!$fichier || ($fichier['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($fichier['error'] === UPLOAD_ERR_INI_SIZE || $fichier['error'] === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('Image trop lourde : 3 Mo maximum. Compressez-la avant de l\'envoyer.');
    }
    if ($fichier['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($fichier['tmp_name'])) {
        throw new RuntimeException("L'image n'a pas pu être envoyée. Réessayez.");
    }
    if ($fichier['size'] > TAILLE_MAX_IMAGE) {
        throw new RuntimeException('Image trop lourde : 3 Mo maximum. Compressez-la avant de l\'envoyer.');
    }
    $infos = @getimagesize($fichier['tmp_name']);
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$infos || !isset($types[$infos[2]])) {
        throw new RuntimeException('Format non accepté. Utilisez une image JPG, PNG ou WebP.');
    }
    $extension = $types[$infos[2]];
    $dossier = trim(preg_replace('/[^a-z0-9\-\/]/', '', $dossier), '/');
    $cible = RACINE . '/uploads/' . $dossier;
    if (!is_dir($cible) && !@mkdir($cible, 0775, true)) {
        throw new RuntimeException("Le dossier des images n'est pas accessible en écriture.");
    }
    $nom = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $extension;
    $destination = $cible . '/' . $nom;

    $reenregistree = false;
    if (function_exists('imagecreatefromstring')) {
        $source = @imagecreatefromstring((string) file_get_contents($fichier['tmp_name']));
        if ($source) {
            $largeur = imagesx($source);
            $hauteur = imagesy($source);
            if ($largeur > $largeur_max) {
                $nouvelle_hauteur = (int) round($hauteur * $largeur_max / $largeur);
                $reduite = imagecreatetruecolor($largeur_max, $nouvelle_hauteur);
                if ($extension !== 'jpg') {
                    imagealphablending($reduite, false);
                    imagesavealpha($reduite, true);
                }
                imagecopyresampled($reduite, $source, 0, 0, 0, 0, $largeur_max, $nouvelle_hauteur, $largeur, $hauteur);
                imagedestroy($source);
                $source = $reduite;
            } elseif ($extension !== 'jpg') {
                imagealphablending($source, false);
                imagesavealpha($source, true);
            }
            $reenregistree = match ($extension) {
                'jpg' => imagejpeg($source, $destination, 84),
                'png' => imagepng($source, $destination, 7),
                'webp' => function_exists('imagewebp') ? imagewebp($source, $destination, 84) : false,
            };
            imagedestroy($source);
        }
    }
    if (!$reenregistree && !move_uploaded_file($fichier['tmp_name'], $destination)) {
        throw new RuntimeException("L'image n'a pas pu être enregistrée.");
    }
    @chmod($destination, 0644);
    return 'uploads/' . $dossier . '/' . $nom;
}

/** Supprime une image envoyée qui n'est plus utilisée. */
function supprimer_image(?string $chemin): void
{
    // Les photos d'exemple sont partagées entre plusieurs contenus : elles partent avec « Retirer les exemples »
    if ($chemin && strpos($chemin, 'uploads/') === 0 && strpos($chemin, 'uploads/exemples/') !== 0 && strpos($chemin, '..') === false) {
        $fichier = RACINE . '/' . $chemin;
        if (is_file($fichier)) {
            @unlink($fichier);
        }
    }
}

// ---------------------------------------------------------------------------
// Recherche par mots-clés (site et administration)
// ---------------------------------------------------------------------------

/** Texte en minuscules, sans accents ni ponctuation : « Réseau, Numérique ! » → « reseau numerique » */
function texte_simplifie(string $texte): string
{
    $texte = mb_strtolower($texte);
    if (class_exists('Transliterator')) {
        $t = Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC');
        $texte = $t ? $t->transliterate($texte) : $texte;
    }
    return trim(preg_replace('/[^\p{L}\p{N}@.]+/u', ' ', $texte));
}

/**
 * Mots utiles d'une recherche : sans les petits mots (de, la, pour…), au singulier simple
 * (« formations » cherche aussi « formation »). Au plus 8 mots.
 */
function mots_recherche(string $q): array
{
    static $vides = ['de', 'des', 'du', 'la', 'le', 'les', 'l', 'd', 'un', 'une', 'et', 'ou', 'en', 'a', 'au', 'aux', 'pour', 'par', 'sur', 'dans', 'avec', 'mon', 'ma', 'mes', 'ce', 'ces', 'qui', 'que', 'est'];
    $mots = [];
    foreach (explode(' ', texte_simplifie($q)) as $m) {
        if ($m === '' || in_array($m, $vides, true)) {
            continue;
        }
        if (mb_strlen($m) > 4 && preg_match('/[sx]$/', $m)) {
            $m = mb_substr($m, 0, -1);
        }
        $mots[$m] = true;
    }
    return array_slice(array_keys($mots), 0, 8);
}

/**
 * Condition SQL : chaque mot doit apparaître dans au moins une des colonnes (accents et majuscules ignorés
 * par la base). « Awa Traoré » trouve donc le prénom et le nom rangés dans deux colonnes différentes.
 */
function condition_mots(array $colonnes, string $q): array
{
    $mots = mots_recherche($q);
    if (!$mots || !$colonnes) {
        return ['1 = 1', []];
    }
    $parties = [];
    $params = [];
    foreach ($mots as $m) {
        $motif = '%' . addcslashes($m, '%_\\') . '%';
        $parties[] = '(' . implode(' OR ', array_map(fn ($c) => "$c LIKE ?", $colonnes)) . ')';
        array_push($params, ...array_fill(0, count($colonnes), $motif));
    }
    return ['(' . implode(' AND ', $parties) . ')', $params];
}

/** Ordre de pertinence : la phrase exacte dans le titre, puis tous les mots dans le titre, puis le reste. */
function pertinence_mots(string $colonne_titre, string $q): array
{
    $mots = mots_recherche($q);
    if (!$mots) {
        return ['0', []];
    }
    $tous = implode(' AND ', array_fill(0, count($mots), "$colonne_titre LIKE ?"));
    return [
        "(CASE WHEN $colonne_titre LIKE ? THEN 0 WHEN $tous THEN 1 ELSE 2 END)",
        array_merge(['%' . addcslashes(trim($q), '%_\\') . '%'], array_map(fn ($m) => '%' . addcslashes($m, '%_\\') . '%', $mots)),
    ];
}

/** La même règle, pour filtrer une liste déjà chargée. */
function texte_correspond(string $texte, string $q): bool
{
    $texte = texte_simplifie($texte);
    foreach (mots_recherche($q) as $m) {
        if (!str_contains($texte, $m)) {
            return false;
        }
    }
    return true;
}

/** Met en valeur les mots cherchés dans un texte (déjà échappé ici), accents et majuscules ignorés. */
function surligner(string $texte, string $q): string
{
    $html = e($texte);
    $mots = mots_recherche($q);
    if (!$mots || !class_exists('Transliterator')) {
        return $html;
    }
    $simple = Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC');
    // Repère chaque mot dans la version sans accents, puis marque le même passage dans le texte d'origine
    $plat = mb_strtolower($simple->transliterate($texte));
    if (mb_strlen($plat) !== mb_strlen($texte)) {
        return $html;
    }
    $marques = array_fill(0, mb_strlen($texte), false);
    foreach ($mots as $m) {
        $pos = 0;
        while (($pos = mb_strpos($plat, $m, $pos)) !== false) {
            for ($k = $pos; $k < $pos + mb_strlen($m); $k++) {
                $marques[$k] = true;
            }
            $pos += mb_strlen($m);
        }
    }
    $sortie = '';
    $ouvert = false;
    foreach (mb_str_split($texte) as $k => $car) {
        if ($marques[$k] && !$ouvert) {
            $sortie .= '<mark class="rounded bg-orange/15 px-0.5 text-inherit">';
            $ouvert = true;
        } elseif (!$marques[$k] && $ouvert) {
            $sortie .= '</mark>';
            $ouvert = false;
        }
        $sortie .= e($car);
    }
    return $sortie . ($ouvert ? '</mark>' : '');
}

/** Envoie un événement d'agenda (.ics) sur la journée entière, du premier au dernier jour. */
function envoyer_agenda(string $uid, string $titre, string $debut, string $fin, string $lieu, string $texte, string $adresse, string $nom_fichier): never
{
    $echapper = fn (string $t) => str_replace(["\\", ';', ',', "\r\n", "\n"], ['\\\\', '\\;', '\\,', '\\n', '\\n'], $t);
    $plier = fn (string $ligne) => implode("\r\n ", mb_str_split($ligne, 60)); // lignes courtes, sans couper un caractère accentué
    $lendemain = (new DateTimeImmutable($fin))->modify('+1 day')->format('Ymd');
    $lignes = [
        'BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//YouthConnekt Mali//Site//FR', 'CALSCALE:GREGORIAN', 'METHOD:PUBLISH',
        'BEGIN:VEVENT',
        'UID:' . $uid . '@youthconnekt-mali',
        'DTSTAMP:' . gmdate('Ymd\THis\Z'),
        'DTSTART;VALUE=DATE:' . (new DateTimeImmutable($debut))->format('Ymd'),
        'DTEND;VALUE=DATE:' . $lendemain,
        'SUMMARY:' . $echapper($titre),
        'LOCATION:' . $echapper($lieu),
        'DESCRIPTION:' . $echapper(trim($texte . "\n\n" . $adresse)),
        'URL:' . $adresse,
        'END:VEVENT', 'END:VCALENDAR',
    ];
    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . preg_replace('/[^a-z0-9-]/', '', $nom_fichier) . '.ics"');
    echo implode("\r\n", array_map($plier, $lignes)) . "\r\n";
    exit;
}

/** Adresse complète d'une image (envoyée ou du dossier assets), pour les aperçus de partage */
function url_absolue_media(string $chemin): string
{
    $relatif = media($chemin);
    $base = base_url();
    if ($base !== '' && str_starts_with($relatif, $base)) {
        $relatif = substr($relatif, strlen($base));
    }
    return url_absolue(strtok($relatif, '?'));
}
