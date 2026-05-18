<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Eloquent ORM model for the `users` table (replaces raw PDO queries).
 */
class User extends Model
{
    protected $table = 'users';

    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'email',
        'password_hash',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public static function findByEmail(string $email): ?self
    {
        return static::query()->where('email', $email)->first();
    }
}
