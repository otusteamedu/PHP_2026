<?php

namespace App\DB;

use PDO;

class Database
{
    private PDO $connection;

    public function __construct(string $dsn, string $username, string $password)
    {
        $this->connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
