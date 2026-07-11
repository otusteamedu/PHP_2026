<?php

declare (strict_types = 1);

namespace App\Infrastructure\Database;

class Connection
{
    protected static $pdo;

    private function __construct()
    {}
    private function __clone()
    {}

    public static function connect(): \PDO
    {
        if (! self::$pdo) {
            $driver   = $_ENV['DB_CONNECTION'];
            $host     = $_ENV['DB_HOST'];
            $port     = $_ENV['DB_PORT'];
            $database = $_ENV['DB_DATABASE'];
            $username = $_ENV['DB_USERNAME'];
            $password = $_ENV['DB_PASSWORD'];

            $dsn = sprintf(
                "%s:host=%s;port=%s;dbname=%s",
                $driver,
                $host,
                $port,
                $database
            );

            self::$pdo = new \PDO(
                $dsn,
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                ]
            );         
        }

        return self::$pdo;
    }
}
