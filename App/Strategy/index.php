<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Strategy\Calculators\DeliveryCalculator;
use App\Strategy\Entitys\Order;
use App\Strategy\Strategys\CourierDelivery;
use App\Strategy\Strategys\PickupDelivery;
use App\Strategy\Strategys\PostDelivery;

$order = new Order('Вася', 'ул. Ленина, д. 10', rand(1, 50));
$strategy = new CourierDelivery;
$calculator = new DeliveryCalculator($strategy);
try {
    $price = $calculator->calculate($order);
} catch (\Exception $e) {
    echo "Исключение: {$e->getMessage()}";
    exit;
}
echo "{$order->getCustomer()}, стоимость доставки курьером заказа ({$order->getWeight()}кг.) до адреса {$order->getAddress()} составит: {$price->getAmount()}{$price->getCurrency()}" . PHP_EOL;


$strategy = new PickupDelivery;
$calculator = new DeliveryCalculator($strategy);
try {
    $price = $calculator->calculate($order);
} catch (\Exception $e) {
    echo "Исключение: {$e->getMessage()}";
    exit;
}
echo "{$order->getCustomer()}, стоимость доставки самовывозом заказа ({$order->getWeight()}кг.) до адреса {$order->getAddress()} составит: {$price->getAmount()}{$price->getCurrency()}" . PHP_EOL;


$strategy = new PostDelivery;
$calculator = new DeliveryCalculator($strategy);
try {
    $price = $calculator->calculate($order);
} catch (\Exception $e) {
    echo "Исключение: {$e->getMessage()}";
    exit;
}
echo "{$order->getCustomer()}, стоимость доставки почтой заказа ({$order->getWeight()}кг.) до адреса {$order->getAddress()} составит: {$price->getAmount()}{$price->getCurrency()}" . PHP_EOL;
