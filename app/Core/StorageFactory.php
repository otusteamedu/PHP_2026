<?php

declare(strict_types=1);

namespace App\Core;

use App\Storage\MovieStorage;
use PDO;

final class StorageFactory
{
    public static function create(): MovieStorage
    {
        $host = getenv('POSTGRES_HOST') ?: 'postgres';
        $port = getenv('POSTGRES_PORT') ?: '5432';
        $db = getenv('POSTGRES_DB') ?: 'movies';
        $user = getenv('POSTGRES_USER') ?: 'postgres';
        $pass = getenv('POSTGRES_PASSWORD') ?: '';

        $pdo = new PDO(
            dsn: "pgsql:host=$host;port=$port;dbname=$db",
            username: $user,
            password: $pass,
            options: [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        );

        return new MovieStorage($pdo);
    }
}
