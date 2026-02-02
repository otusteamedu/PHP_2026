<?php
echo "Домашнее задание 1<br>" . date("Y-m-d H:i:s") . "<br><br>";


// Redis connection test
try {
    $redis = new Redis();
    $redis->connect('redis', 6379);
    $redis->set('test_key', 'Hello Redis');
    echo "OK — " . $redis->get('test_key') . "<br><br>";
} catch (Throwable $e) {
    echo "ERROR — " . $e->getMessage() . "<br><br>";
}


// Memcached connection test
try {
    $memcached = new Memcached();
    $memcached->addServer('memcached', 11211);
    $memcached->set('test_key', 'Hello Memcached');
    echo "OK — " . $memcached->get('test_key') . "<br><br>";
} catch (Throwable $e) {
    echo "ERROR — " . $e->getMessage() . "<br><br>";
}


// MySQL (PDO) connection test
try {
    $pdo = new PDO(
        'mysql:host=db;dbname=app;charset=utf8mb4',
        'app',
        'root',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    $stmt = $pdo->query('SELECT NOW()');
    echo "OK — DB time: " . $stmt->fetchColumn() . "<br><br>";
} catch (Throwable $e) {
    echo "ERROR — " . $e->getMessage() . "<br><br>";
}

phpinfo();

