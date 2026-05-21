<?php

namespace App\AbstractFactoryAndBuilder\DB\MySQL;

use App\AbstractFactoryAndBuilder\Contracts\ConnectionInterface;
use PDO;

class MySQLConnection implements ConnectionInterface
{
    private PDO $connection;
    public function __construct(private array $config)
    {
        $this->connection = new PDO(
            "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}",
            $config['user'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }   
}
