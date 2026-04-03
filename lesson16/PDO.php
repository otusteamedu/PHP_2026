<?php

// mysql
$host = '172.30.185.21';
$db   = 'php-2026';
$user = 'admin';
$pass = 'admin';
$port = '33092';

$dsn = "mysql:host=$host;dbname=$db;port=$port";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Подключены к mysql\n";
} catch (PDOException $e) {
    echo "Ошибка подключения к mysql: " . $e->getMessage() . "\n";
}
$sql = "INSERT INTO users (username) VALUES (:username)";
$stmt = $pdo->prepare($sql);

$username = 'mysql user';
$stmt->execute([
    ':username' => $username
]);



// postgres
$host = '172.30.185.21';
$db   = 'php-2026';
$user = 'admin';
$pass = 'admin';
$port = '15432';

$dsn = "pgsql:host=$host;dbname=$db;port=$port";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Подключены к postgres\n";
} catch (PDOException $e) {
    echo "Ошибка подключения к postgres: " . $e->getMessage() . "\n";
}
$sql = "INSERT INTO users (username) VALUES (:username)";
$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':username' => 'posgres user'
]);
