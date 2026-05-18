<?php

declare(strict_types=1);

namespace App\Controllers;

// Public pages (home) and protected pages (e.g. dashboard) using the auth guard when needed.

use Core\Http\Request;
use Core\Http\Response;
use Core\View\Engine;

class HomeController 
{
    public function dashboard(Request $request): void
    {
        $email = (string) ($_SESSION['user']['email'] ?? 'user');
        (new Engine())->render('dashboard', ['email' => $email]);
    }

    public function index(Request $request): void
    {
        (new Response())->redirect(app_url('/login'));
        exit;
    }
}

