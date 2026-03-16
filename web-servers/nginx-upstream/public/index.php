<?php

require __DIR__.'/../vendor/autoload.php';

use App\Kernel;
use App\RedisClusterService;

header('Content-Type: text/plain; charset=utf-8');

session_start();
$_SESSION['test_counter'] = ($_SESSION['test_counter'] ?? 0) + 1;

echo "<pre>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo (new Kernel())->run();
} else {
    echo "Это GET запрос, ядро не запущено.\n";
}

$hostname = gethostname();
$counter = $_SESSION['test_counter'];

(new RedisClusterService())->run();

echo "Работаем на контейнере: $hostname\n";
echo "Сессия работает. Счетчик: $counter";

echo "</pre>";