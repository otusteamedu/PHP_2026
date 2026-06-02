<?php

declare (strict_types = 1);
interface DbFactoryInterface
{
    public function connection(): DbConnectionInterface;
    public function queryBuilder(): QueryBuilderInterface;
    public function transaction(): TransactionInterface;
}