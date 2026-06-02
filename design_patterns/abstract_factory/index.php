<?php

declare(strict_types=1);

$config = [
    'driver' => env('DB_DRIVER', 'pgsql'),
    'host' => env('DB_HOST', 'localhost'),
    'db' => env('DB_DATABASE', 'localhost'),
    'port' => env('DB_PORT'),
    'user' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'charset' => env('utf8mb4')
];

$factory = Container::make($config['driver']);

$connection = $factory->connection();
$connection->connect($config);

//Пример запроса
$sql = $factory->queryBuilder()
    ->select('movie')
    ->paginate(10, 20)
    ->get();


$movie = $connection->execute($sql);

// Пример транзакции
$transaction = $factory->transaction();
$transaction->start();
try {
//    Что-то реализуем
    $transaction->commit();
} catch (\Exception $e) {
    $transaction->rollback();
}