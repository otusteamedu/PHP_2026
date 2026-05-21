<?php

namespace App\AbstractFactoryAndBuilder\DB\PostgreSQL;

use App\AbstractFactoryAndBuilder\Contracts\ConnectionInterface;
use PDO;

class PostgreSQLConnection implements ConnectionInterface
{

    private PDO $connection;
    public function __construct(private array $config)
    {
        $this->connection = new PDO(
            "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};user={$config['user']};password={$config['password']}",
            $config['user'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );

        $this->connection->exec("SET search_path TO {$config['schema']}");
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
