<?php

namespace App\AbstractFactoryAndBuilder\DB\MySQL;

use App\AbstractFactoryAndBuilder\Contracts\ConnectionInterface;
use App\AbstractFactoryAndBuilder\Contracts\TransactionInterface;

class MySQLTransaction implements TransactionInterface
{
    public function __construct(private ConnectionInterface $connection) {}

    public function begin(): void
    {
        $this->connection->getConnection()->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->getConnection()->commit();
    }

    public function rollback(): void
    {
        $this->connection->getConnection()->rollback();
    }
}