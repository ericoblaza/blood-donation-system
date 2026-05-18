<?php

declare(strict_types=1);

namespace Core;

/**
 * Auth guard helper that redirects users to login when they are not logged in.
 */
class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user']) && is_array($_SESSION['user']);
    }

    /**
     * Stop the request and send guests to login.
     */
    public static function requireUser(): void
    {
        if (!self::check()) {
            header('Location: ' . \app_url('/login'));
            exit;
        }
    }
}
