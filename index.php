<?php

require __DIR__ . '/vendor/autoload.php';

echo "PHP OK ✅";
echo "<br>";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dotenv->required(['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD']);


$dsn = sprintf(
    "pgsql:host=%s;port=%s;dbname=%s",
    $_ENV['DB_HOST'],
    $_ENV['DB_PORT'],
    $_ENV['DB_DATABASE']
);

try {
    $pdo = new PDO($dsn,
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    echo "Postgres connected ✅";
    echo "<br>";
} catch (PDOException $e) {
    echo "Postgres connection failed: " . $e->getMessage();
    echo "<br>";
}

$redis = new Redis();
try {
    $redis->connect('redis', 6379);
    $redis->set('test', 'Success');
    echo "Redis connected ✅, value: " . $redis->get('test');
    echo "<br>";
} catch (Exception $e) {
    echo "Redis connection failed: " . $e->getMessage();
    echo "<br>";
}

try {
    $mc = new Memcached();
    $mc->addServer('memcached', 11211);

    if ($mc->getVersion() === false) {
        throw new Exception('Не удалось подключиться к Memcached');
    }

    $mc->set('test', 'Memcached WORK');
    echo $mc->get('test');

} catch (Exception $e) {
    error_log('Memcached error: ' . $e->getMessage());
    echo 'Ошибка Memcached: ' . $e->getMessage();
}