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

installer_gestion_erreurs();

// En-têtes de sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

demarrer_session();

require __DIR__ . '/app/routes.php';

$chemin = chemin_actuel();

// Tant que la base n'est pas installée, on propose l'installation
if ($chemin !== '/installation' && !base_installee()) {
    rediriger(url('/installation'));
}

router($chemin, $_SERVER['REQUEST_METHOD'] ?? 'GET');
