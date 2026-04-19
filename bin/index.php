<?php

declare(strict_types=1);

echo "<h1>Соединение с сервисами</h1>";

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
    $memcached->addServer('memcached', getenv('MEMCACHED_PORT') ?: 11211);
    $memcached->set('test_key', 'Memcached работает!');
    $value = $memcached->get('test_key');
    echo "<p>$value</p>";
} catch (Exception $e) {
    echo "<p>Memcached ошибка: " . $e->getMessage() . "</p>";
}

?>
