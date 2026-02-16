<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Otus\MoneyCalc\PriceCalculator;

$calc = new PriceCalculator();

// Проверяем: 100.00 руб (10000 копеек) + 50.50 руб (5050 копеек)
try {
    $result = $calc->add('10000', '5050');
    echo "Результат работы пакета: " . $result . PHP_EOL;
} catch (\Exception $e) {
    echo "Ошибка: " . $e->getMessage() . PHP_EOL;
}
