<?php

declare(strict_types=1);

// Front controller: single entry for every browser request — session, autoload, routes, then dispatch.

session_start();

// Absolute path to project root (folder above /public).
define('BASE_PATH', dirname(__DIR__));

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Web path prefix for this app (e.g. /my-mvc-framework/public) so forms and redirects work in a subdirectory.
$scriptDir = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php')));
$scriptDir = rtrim($scriptDir, '/');
define(
    'APP_BASE_URL',
    ($scriptDir === '' || $scriptDir === '.' || $scriptDir === '/') ? '' : $scriptDir
);

/**
 * Build a URL path under this app (handles subdirectory installs vs document-root /public).
 */
function app_url(string $path): string
{
    $path = '/' . ltrim($path, '/');

    return APP_BASE_URL === '' ? $path : APP_BASE_URL . $path;
}

require_once BASE_PATH . '/vendor/autoload.php';

\Core\Database\EloquentBootstrap::boot();

$app = \Core\Application::configure();
$router = $app->router();

// Register URL → controller@method rules from routes/web.php.
$routesFile = BASE_PATH . '/routes/web.php';
if (is_file($routesFile)) {
    require_once $routesFile;
}

// Match route and run handler (controller action or closure).
$app->run(new \Core\Http\Request());
