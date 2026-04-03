<?php

require_once 'User.php';
require_once 'UserDAO.php';
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


$userDao = new UserDao($pdo);
$newUser = new User(id: null, username: "DAO new User ");
$newId = $userDao->create($newUser);
