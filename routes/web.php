<?php

declare(strict_types=1);

// Route map: which HTTP method + path runs which controller action (loaded from public/index.php).

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\BloodRequestController;

if (!isset($router)) {
    throw new RuntimeException('Router instance is not available in routes/web.php');
}
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/', [HomeController::class, 'index']);
$router->get('/dashboard', [HomeController::class, 'dashboard']);

$router->get('/requests', [BloodRequestController::class, 'index']);
$router->get('/requests/create', [BloodRequestController::class, 'showCreate']);
$router->post('/requests', [BloodRequestController::class, 'store']);

$router->post('/requests/accept', [BloodRequestController::class, 'accept']);
$router->post('/requests/decline', [BloodRequestController::class, 'decline']);

$router->get('/requests/history', [BloodRequestController::class, 'history']);

$router->get('/requests/responses', [BloodRequestController::class, 'requesterResponses']);
$router->get('/requests/{id}/edit', [BloodRequestController::class, 'showEdit']);
$router->post('/requests/{id}/update', [BloodRequestController::class, 'update']);
$router->post('/requests/{id}/delete', [BloodRequestController::class, 'destroy']);
$router->get('/requests/{id}', [BloodRequestController::class, 'show']);

$router->post('/logout', [AuthController::class, 'logout']);




