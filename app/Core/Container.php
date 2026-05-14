<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Module\MovieModule;
use PDO;

final class Container
{
    private ?MovieModule $movies = null;

    private ?PDO $pdo = null;

    public function movies(): MovieModule
    {
        return $this->movies ??= new MovieModule($this->pdo());
    }

    private function pdo(): PDO
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        $host = getenv('POSTGRES_HOST') ?: 'postgres';
        $port = getenv('POSTGRES_PORT') ?: '5432';
        $db = getenv('POSTGRES_DB') ?: 'movies';
        $user = getenv('POSTGRES_USER') ?: 'postgres';
        $pass = getenv('POSTGRES_PASSWORD') ?: '';

        return $this->pdo = new PDO(
            dsn: "pgsql:host=$host;port=$port;dbname=$db",
            username: $user,
            password: $pass,
            options: [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        );
    }
}
