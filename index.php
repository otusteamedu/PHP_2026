<?php

use AleksandKrasnyatov\BracketsChecker\BracketsChecker;

require(__DIR__ . '/vendor/autoload.php');

$checker = new BracketsChecker();

if ($checker->check('()()')) {
    echo 'Все работает!' . PHP_EOL;
} else {
    echo 'Не работает!' . PHP_EOL;
}
