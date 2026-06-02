<?php

declare (strict_types = 1);
interface TransactionInterface
{
    public function start(): void;
    public function commit(): void;
    public function rollback(): void;
}