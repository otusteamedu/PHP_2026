<?php

namespace App\AbstractFactoryAndBuilder\Contracts;

interface DatabaseFactoryInterface
{
    public function createConnection(): ConnectionInterface;
    public function createQueryBuilder(): QueryBuilderInterface;
    public function createTransaction(ConnectionInterface $connection): TransactionInterface;
}
