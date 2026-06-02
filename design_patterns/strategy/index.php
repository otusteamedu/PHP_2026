<?php

declare (strict_types = 1);

$deliveryMapper = [
    'courier' => new CourierDelivery(),
    'pickup'  => new PickupDelivery(),
    'post'    => new PostDelivery(),
];

$config = [
    'driver' => 'post',
];

$order = [
    'product_id' => 1,
    'address'    => 'Moscow',
    'weight'     => 2,
    'tariff'     => 0.22,
    'kv'         => 1,
    'distance'   => 15,
    'quantity'   => 10,
];

// Расчет 1 доставки
$calculator = new DeliveryCalculatorService($deliveryMapper[$config['driver']]);
$price      = $calculator->calculate($order);

// Расчет по всем доставкам
$allPrices = [];

foreach ($deliveryMapper as $name => $class) {
    $calculator->set($class);
    $allPrices[$name] = $calculator->calculate($order);
}

print_r($allPrices);
