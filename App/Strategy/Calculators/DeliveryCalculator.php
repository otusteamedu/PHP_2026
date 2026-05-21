<?php

namespace App\Strategy\Calculators;

use App\Strategy\Entitys\Order;
use App\Strategy\Interfaces\DeliveryInterface;
use App\Strategy\ObjectValues\Money;

class DeliveryCalculator
{
    public function __construct(private readonly DeliveryInterface $strategy) {}

    public function calculate(Order $order): Money
    {
        return $this->strategy->getCost($order);
    }
}