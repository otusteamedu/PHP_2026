<?php

$dsn = 'mysql:host=172.30.185.21;port=33093;dbname=php-2026;charset=utf8mb4';
$user = 'admin';
$pass = 'admin';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("DB connection error: " . $e->getMessage());
}
# Транзакция

try {
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

    $pdo->commit();
    echo "Inserted 1000 \n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}

