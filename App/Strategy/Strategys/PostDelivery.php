<?php

namespace App\Strategy\Strategys;

use App\Strategy\Entitys\Order;
use App\Strategy\Interfaces\DeliveryInterface;
use App\Strategy\ObjectValues\Money;

class PostDelivery implements DeliveryInterface
{
    public function getCost(Order $order): Money
    {
        $cost = 0;
        $weight = $order->getWeight();
        $cost = match (true) {
            $weight < 10 => 100,
            $weight <= 25 => 250.45,
            $weight > 25 => 500.9,
        };
        return new Money($cost);
    }
}