<?php

require __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__ . '/config/database.php';

use App\AbstractFactoryAndBuilder\DatabaseFactoryResolver;
$factory = DatabaseFactoryResolver::fromConfig($config);

$queryBuilder = $factory->createQueryBuilder();
$sql = $queryBuilder
    ->select(['users.id as user_id', 'posts.id as post_id'])
    ->from('users')
    ->where('users.id', '=', 1)
    ->join('posts', 'id', 'user_id')
    ->limit(10)
    ->offset(0)
    ->build();

$connection = $factory->createConnection();
$pdo = $connection->getConnection();
$transaction = $factory->createTransaction($connection);
$transaction->begin();

try {
    $result = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $transaction->commit();
    var_dump($result);
} catch (\Exception $e) {
    $transaction->rollback();
    throw $e;
}
