<?php

declare (strict_types = 1);

class PgSqlFactory implements DbFactoryInterface
{
    private ?DbConnectionInterface $connection = null;

    public function connection(): DbConnectionInterface
    {
        if ($this->connection === null) {
            $this->connection = new PgSQLConnection();
        }
        return $this->connection;
    }
    public function queryBuilder(): QueryBuilderInterface
    {
        return new PgSQLQueryBuilder();
    }

    public function transaction(): TransactionInterface
    {
        return new PgSQLTransaction($this->connection);
    }
}