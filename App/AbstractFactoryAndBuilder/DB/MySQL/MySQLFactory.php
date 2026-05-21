<?php

namespace App\AbstractFactoryAndBuilder\DB\MySQL;

use App\AbstractFactoryAndBuilder\Contracts\DatabaseFactoryInterface;
use App\AbstractFactoryAndBuilder\Contracts\ConnectionInterface;
use App\AbstractFactoryAndBuilder\Contracts\QueryBuilderInterface;
use App\AbstractFactoryAndBuilder\Contracts\TransactionInterface;

class MySQLFactory implements DatabaseFactoryInterface
{
    public function __construct(private array $config) {}

    public function createConnection(): ConnectionInterface
    {
        return new MySQLConnection($this->config);
    }

    public function createQueryBuilder(): QueryBuilderInterface
    {
        return new MySQLQueryBuilder();
    }

    public function createTransaction(ConnectionInterface $connection): TransactionInterface
    {
        return new MySQLTransaction($connection);
    }
}