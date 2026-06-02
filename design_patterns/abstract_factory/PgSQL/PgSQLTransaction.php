<?php

declare (strict_types = 1);

class PgSQLTransaction implements TransactionInterface
{
    public function __construct(private DbConnectionInterface $connection)
    {}

    public function start(): void
    {
        $this->connection->getPdo()->beginTransaction();
    }
    public function commit(): void
    {
        $this->connection->getPdo()->commit();
    }
    public function rollback(): void
    {
        $this->connection->getPdo()->rollBack();
    }
}