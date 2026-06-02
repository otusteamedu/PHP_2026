<?php

declare (strict_types = 1);

class MySqlFactory implements DbFactoryInterface
{
    private ?DbConnectionInterface $connection = null;

    public function connection(): DbConnectionInterface
    {
        if ($this->connection === null) {
            $this->connection = new MySQLConnection();
        }
        return $this->connection;
    }
    public function queryBuilder(): QueryBuilderInterface
    {
        return new MySQLQueryBuilder();
    }

    public function transaction(): TransactionInterface
    {
        return new MySQLTransaction($this->connection());
    }
}