<?php

namespace App\AbstractFactoryAndBuilder;

use App\AbstractFactoryAndBuilder\Contracts\DatabaseFactoryInterface;
use App\AbstractFactoryAndBuilder\DB\MySQL\MySQLFactory;
use App\AbstractFactoryAndBuilder\DB\PostgreSQL\PostgreSQLFactory;

final class DatabaseFactoryResolver
{
    public static function fromConfig(array $config): DatabaseFactoryInterface
    {
        return match ($config['driver']) {
            'mysql' => new MySQLFactory($config['connections']['mysql']),
            'pgsql' => new PostgreSQLFactory($config['connections']['pgsql']),
            default => throw new \InvalidArgumentException('Unknown driver'),
        };
    }
}