<?php

namespace App\AbstractFactoryAndBuilder\DB\PostgreSQL;

use App\AbstractFactoryAndBuilder\Contracts\DatabaseFactoryInterface;
use App\AbstractFactoryAndBuilder\Contracts\ConnectionInterface;
use App\AbstractFactoryAndBuilder\Contracts\QueryBuilderInterface;
use App\AbstractFactoryAndBuilder\Contracts\TransactionInterface;
use App\AbstractFactoryAndBuilder\DB\PostgreSQL\PostgreSQLTransaction;

class PostgreSQLFactory implements DatabaseFactoryInterface
{
    public function __construct(private array $config) {}

    public function createConnection(): ConnectionInterface
    {
        return new PostgreSQLConnection($this->config);
    }

    public function createQueryBuilder(): QueryBuilderInterface
    {
        return new PostgreSQLQueryBuilder();
    }

    public function createTransaction(ConnectionInterface $connection): TransactionInterface
    {
        return new PostgreSQLTransaction($connection);
    }
}