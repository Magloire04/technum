<?php

declare(strict_types=1);

// Routeur du serveur PHP intégré, pour le développement uniquement :
// les fichiers existants de public/ sont servis tels quels, le reste passe par index.php.
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url(is_string($requestUri) ? $requestUri : '/', PHP_URL_PATH);
$publicDir = dirname(__DIR__) . '/public';

if (is_string($path) && $path !== '/' && is_file($publicDir . $path)) {
    return false;
}

require $publicDir . '/index.php';
