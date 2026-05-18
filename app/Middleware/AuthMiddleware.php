<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Auth;
use Core\Http\Request;

/**
 * Redirects guests to login before protected routes run.
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): void
    {
        Auth::requireUser();
        $next($request);
    }
}
