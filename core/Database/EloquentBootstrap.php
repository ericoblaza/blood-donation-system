<?php

declare(strict_types=1);

namespace Core\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use RuntimeException;

final class EloquentBootstrap
{
    private static bool $booted = false;

    public static function boot(): void
    {
        if (self::$booted) {
            return;
        }

        $config = require dirname(__DIR__, 2) . '/config/database.php';

        if (!is_array($config)) {
            throw new RuntimeException('config/database.php must return an array.');
        }

        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => (string) $config['host'],
            'port' => (int) $config['port'],
            'database' => (string) $config['database'],
            
            'username' => (string) $config['username'],
            'password' => (string) $config['password'],
            'charset' => (string) $config['charset'],
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        self::$booted = true;
    }
}
