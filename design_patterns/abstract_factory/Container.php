<?php

declare(strict_types=1);

class Container
{
    public static function make(string $driver): DbFactoryInterface
    {
        return match ($driver) {
            'mysql' => new MySqlFactory(),
            'pgsql' => new PgSqlFactory(),
            default => throw new RuntimeException('Неизвестный драйвер базы данных'),
        };
    }
}