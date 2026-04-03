<?php

require_once 'EntityManager.php';
require_once 'User.php';
require_once 'UserMetadata.php';
require_once 'UserRepository.php';

// mysql
$host = '172.30.185.21';
$db = 'php-2026';
$user = 'admin';
$pass = 'admin';
$port = '33092';

$dsn = "mysql:host=$host;dbname=$db;port=$port";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Подключены к mysql\n";
} catch (PDOException $e) {
    echo "Ошибка подключения к mysql: ".$e->getMessage()."\n";
}

$em = new EntityManager($pdo);
$userRepository = new UserRepository($em);

$user = new User(username: "ORM user");
$userRepository->save($user);

echo "Создан пользователь с ID: $user->id\n";

$u = $userRepository->find($user->id);
print_r($u);
