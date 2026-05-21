<?php

namespace App\Strategy\Strategys;

use App\Strategy\Entitys\Order;
use App\Strategy\Interfaces\DeliveryInterface;
use App\Strategy\ObjectValues\Money;

class CourierDelivery implements DeliveryInterface
{
    public function getCost(Order $order): Money
    {
        $cost = 0;
        // $addressDelivery = $order->getAddress();
        $distance = rand(1, 150); // допустим на основании адреса доставки получаем расстояние от магазина (склада) до получателя
        if ($distance > 145) {
            throw new \Exception('Слишком далеко.');
        }

        $cost += match (true) {
            $distance < 10 => 100,
            $distance < 50 => 1000,
            $distance < 100 => 5000,
            $distance >= 100 => 10000,
        };

        $weight = $order->getWeight();
        $cost += match (true) {
            $weight < 10 => 50,
            $weight <= 20 => 500,
            $weight > 20 => 999,
        };
        return new Money($cost);
    }
}
