<?php

return [
    'driver' => 'pgsql',

    'connections' => [
        'mysql' => [
            'host' => 'mysql-8.0',
            'port' => 3306,
            'dbname' => 'hw12',
            'user' => 'root',
            'password' => '',
        ],
        'pgsql' => [
            'host' => 'PostgreSQL-15',
            'port' => 5432,
            'dbname' => 'postgres',
            'schema' => 'otus',
            'user' => 'postgres',
            'password' => 'postgres',
        ],
    ],
];
