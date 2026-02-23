<?php

use AleksandKrasnyatov\BracketsChecker\BracketsChecker;

require(__DIR__ . '/vendor/autoload.php');

session_start();
$_SESSION['check'] = 'ok';
echo session_id();

$checker = new BracketsChecker();

if ($checker->check('()()')) {
    echo 'Все работает!' . PHP_EOL;
} else {
    echo 'Не работает!' . PHP_EOL;
}

//// Redis
//try {
//    $redis = new Redis();
//    $redis->connect('redis', getenv('REDIS_PORT') ?: 6379);
//    $redis->set('test_key', 'Redis работает!');
//    $value = $redis->get('test_key');
//    echo "<p>$value</p>";
//    $redis->close();
//} catch (Exception $e) {
//    echo "<p> Redis ошибка: " . $e->getMessage() . "</p>";
//}

phpinfo();
?>
