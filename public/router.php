<?php
/**
 * Router for PHP built-in dev server.
 * Usage:
 *   php -S 127.0.0.1:8080 -t public public/router.php
 *
 * - Serves real static files directly (CSS, JS, images, etc.)
 * - Routes everything else through public/index.php so clean URLs work.
 */

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');

// Serve real existing files (assets, favicon, etc.)
$publicDir = realpath(__DIR__);
$relativePath = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
$target = realpath($publicDir . DIRECTORY_SEPARATOR . $relativePath);
if ($path !== '/' && $target !== false && str_starts_with($target, $publicDir . DIRECTORY_SEPARATOR) && is_file($target)) {
    return false; // let the built-in server handle it
}

// Otherwise hand off to index.php (clean URL handling)
require __DIR__ . '/index.php';
