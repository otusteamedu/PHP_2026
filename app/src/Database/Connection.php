<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

final class Connection
{
    private static ?PDO $pdo = null;

    public static function fromEnv(): PDO
    {
        if (self::$pdo === null) {
            $host = $_ENV['POSTGRES_HOST'] ?: 'localhost';
            $port = (int)($_ENV['POSTGRES_PORT'] ?: 5432);
            $database = $_ENV['POSTGRES_DATABASE'] ?: 'hw21';
            $user = $_ENV['POSTGRES_USER'] ?: 'user';
            $password = $_ENV['POSTGRES_PASSWORD'] ?: 'pass';

            self::$pdo = new PDO(
                sprintf('pgsql:host=%s;port=%d;dbname=%s', $host, $port, $database),
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ],
            );
        }

        return self::$pdo;
    }
}
