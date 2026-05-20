<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Decorator\Onion;
use App\Decorator\Pepper;
use App\Domain\Order\Order;
use App\Kernel;
use App\Proxy\CookInterface;
use App\Strategy\BurgerStrategy;

header('Content-Type: text/plain; charset=utf-8');

$container = Kernel::boot();

$order = new Order(
    strategy: $container->get(BurgerStrategy::class),
    extras: [Onion::class, Pepper::class],
);

$product = $order->compose();

echo "Заказ собран:\n";
echo "  Название: {$product->getName()}\n";
echo '  Ингредиенты: ' . implode(', ', $product->getIngredients()) . "\n";
echo '  Цена: ' . number_format($product->getPrice(), 2, '.', ' ') . "\n\n";

echo "--- Готовка ---\n";

/** @var CookInterface $cook */
$cook = $container->get(CookInterface::class);
$result = $cook->cook($product);

echo "\n--- Результат ---\n";
echo $result === null
    ? "Продукт утилизирован.\n"
    : "Готов: {$result->getName()} (" . implode(', ', $result->getIngredients()) . ")\n";


echo date('Y-m-d H:i:s') . " - Заказ обработан.\n";