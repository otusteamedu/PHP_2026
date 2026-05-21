<?php

namespace App\AbstractFactoryAndBuilder\Contracts;

interface TransactionInterface
{
    public function begin(): void;
    public function commit(): void;
    public function rollback(): void;
}
