<?php
/*
 * YouthConnekt Mali : point d'entrée unique du site.
 * Toutes les adresses passent par ce fichier (voir .htaccess), qui choisit la page à afficher.
 */

declare(strict_types=1);

if (PHP_VERSION_ID < 80100) {
    http_response_code(500);
    exit('YouthConnekt Mali nécessite PHP 8.1 ou plus récent (version actuelle : ' . PHP_VERSION . ').');
}

require __DIR__ . '/app/noyau.php';
require __DIR__ . '/app/auth.php';
require __DIR__ . '/app/email.php';
require __DIR__ . '/app/contenus.php';
require __DIR__ . '/app/referencement.php';

installer_gestion_erreurs();

// Site en ligne en HTTPS : toute visite en http est renvoyée vers https (adresse définitive, mémorisée par les navigateurs)
if (str_starts_with((string) config('url_site', ''), 'https://') && !est_https() && PHP_SAPI !== 'cli') {
    $hote = (string) parse_url((string) config('url_site'), PHP_URL_HOST);
    header('Location: https://' . $hote . ($_SERVER['REQUEST_URI'] ?? '/'), true, 301);
    exit;
}

// En-têtes de sécurité
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
// Aucune intégration du site par un autre site, aucun plugin, formulaires envoyés au site seulement
header("Content-Security-Policy: frame-ancestors 'self'; object-src 'none'; base-uri 'self'; form-action 'self'");
if (est_https()) {
    header('Strict-Transport-Security: max-age=31536000');
}

demarrer_session();

require __DIR__ . '/app/routes.php';

$chemin = chemin_actuel();

// Administration, espace membre, installation : jamais dans les moteurs de recherche
if (preg_match('#^/(gestion|compte|installation)(/|$)#', $chemin)) {
    header('X-Robots-Tag: noindex, nofollow');
}

// Tant que la base n'est pas installée, on propose l'installation
if ($chemin !== '/installation' && !base_installee()) {
    rediriger(url('/installation'));
}

// Demandes d'intégration sans réponse depuis 24 heures : acceptées automatiquement
if ($chemin !== '/installation') {
    valider_demandes_en_retard();
}

router($chemin, $_SERVER['REQUEST_METHOD'] ?? 'GET');
