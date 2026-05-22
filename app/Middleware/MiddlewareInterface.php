<?php
//defines how middleware works in the application
declare(strict_types=1);

namespace App\Middleware;

use Core\Http\Request;

interface MiddlewareInterface
{
    /**
     * @param callable(Request): void $next
     */
    public function handle(Request $request, callable $next): void;
}
