<?php

declare(strict_types=1);

namespace App\Lessons\Hw1;

use PDO;
use PDOException;

class CheckDatabaseConnection
{
    public static function execute(): string
    {
        $dsn = 'mysql:host=mysql;dbname=' . getenv('DB_NAME') . ';charset=utf8';
        $user = getenv('DB_USER');
        $password = getenv('DB_PASS');

        try {
            $pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            return 'Подключение к базе данных MYSQL успешно!';
        } catch (PDOException $e) {
            return 'MYSQL: ' . $e->getMessage();
        }
    }
}
