<?php

declare (strict_types = 1);

interface DbConnectionInterface
{
    public function connect(array $config): void;

    public function execute(string $sql, array $bindings = []): array;

     public function getPdo(): PDO;
}