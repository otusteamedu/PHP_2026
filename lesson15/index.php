<?php

$dsn = 'mysql:host=172.30.185.21;port=33092;dbname=php-2026;charset=utf8mb4';
$user = 'admin';
$pass = 'admin';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("DB connection error: " . $e->getMessage());
}

# Без транзакции
// Prepared statements
$insertUser = $pdo->prepare("
    INSERT INTO users (username) 
    VALUES (:username)
");

$insertMessage = $pdo->prepare("
    INSERT INTO messages (user_id, content) 
    VALUES (:user_id, :content)
");

for ($i = 1; $i <= 100; $i++) {

    $username = "user_" . $i;

    $insertUser->execute([
        ':username' => $username
    ]);

    $userId = $pdo->lastInsertId();

    for ($m = 1; $m <= 10; $m++) {

        $content = "Message $m from $username";

        $insertMessage->execute([
            ':user_id' => $userId,
            ':content' => $content
        ]);
    }
}



//exit();

# Транзакция

try {
    // Одна большая транзакция
    $pdo->beginTransaction();

    // Подготовленные выражения
    $insertUser = $pdo->prepare("
        INSERT INTO users (username)
        VALUES (:username)
    ");

    $insertMessage = $pdo->prepare("
        INSERT INTO messages (user_id, content)
        VALUES (:user_id, :content)
    ");

    // Генерация 100 пользователей
    for ($i = 1; $i <= 100; $i++) {

        $username = "user_" . $i;

        $insertUser->execute([
            ':username' => $username
        ]);

        $userId = $pdo->lastInsertId();

        // 10 сообщений на пользователя
        for ($m = 1; $m <= 10; $m++) {
            $content = "Message $m from $username";

            $insertMessage->execute([
                ':user_id' => $userId,
                ':content' => $content
            ]);
        }
    }

    // Фиксация транзакции
    $pdo->commit();
    echo "Inserted 100 users and 1000 messages.\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}

