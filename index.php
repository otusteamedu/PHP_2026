<?php

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

phpinfo();
?>
