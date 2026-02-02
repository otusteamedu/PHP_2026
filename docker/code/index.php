<?php

echo "<h1>Проверка соединения с сервисами</h1>";

// Redis
try {
    $redis = new Redis();
    $redis->connect('redis', getenv('REDIS_PORT') ?: 6379);
    $redis->set('test_key', 'Redis работает!');
    $value = $redis->get('test_key');
    echo "<p>$value</p>";
    $redis->close();
} catch (Exception $e) {
    echo "<p> Redis ошибка: " . $e->getMessage() . "</p>";
}

// Memcached
try {
    $memcached = new Memcached();
    $memcached->addServer('memcached', getenv('MEMCACHE_PORT') ?: 11211);
    $memcached->set('test_key', 'Memcached работает!');
    $value = $memcached->get('test_key');
    echo "<p>$value</p>";
} catch (Exception $e) {
    echo "<p>Memcached ошибка: " . $e->getMessage() . "</p>";
}

// PostgreSQL
$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: 3306;
$db   = getenv('DB_NAME') ?: 'app_db';
$user = getenv('DB_USERNAME') ?: getenv('MYSQL_USER') ?: 'app';
$pass = getenv('DB_PASSWORD') ?: getenv('MYSQL_PASSWORD') ?: 'apppass';

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // optional: persistent connection
    // PDO::ATTR_PERSISTENT     => true,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // optional: set a connection timeout (requires PDO_MYSQL_OPT_CONNECT_TIMEOUT constant)
    if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
        $pdo->exec("SET time_zone = '+00:00'");
    }
} catch (PDOException $e) {
    // handle error appropriately in production (don't echo credentials)
    throw new RuntimeException('Database connection failed: ' . $e->getMessage());
}

phpinfo();
?>
