<?php

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
