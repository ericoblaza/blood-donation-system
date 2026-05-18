<?php

declare(strict_types=1);

// Route map: which HTTP method + path runs which controller action (loaded from public/index.php).

use App\Controllers\AuthController;
use App\Controllers\BloodRequestController;
use App\Controllers\HomeController;
use App\Middleware\AuthMiddleware;

if (!isset($router)) {
    throw new RuntimeException('Router instance is not available in routes/web.php');
}

$auth = [AuthMiddleware::class];

$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/', [HomeController::class, 'index']);
$router->get('/dashboard', [HomeController::class, 'dashboard'], $auth);

$router->get('/requests', [BloodRequestController::class, 'index'], $auth);
$router->get('/requests/create', [BloodRequestController::class, 'showCreate'], $auth);
$router->post('/requests', [BloodRequestController::class, 'store'], $auth);

$router->post('/requests/accept', [BloodRequestController::class, 'accept'], $auth);
$router->post('/requests/decline', [BloodRequestController::class, 'decline'], $auth);

$router->get('/requests/history', [BloodRequestController::class, 'history'], $auth);

$router->get('/requests/responses', [BloodRequestController::class, 'requesterResponses'], $auth);
$router->get('/requests/{id}/edit', [BloodRequestController::class, 'showEdit'], $auth);
$router->post('/requests/{id}/update', [BloodRequestController::class, 'update'], $auth);
$router->post('/requests/{id}/delete', [BloodRequestController::class, 'destroy'], $auth);
$router->get('/requests/{id}', [BloodRequestController::class, 'show'], $auth);

$router->post('/logout', [AuthController::class, 'logout'], $auth);
