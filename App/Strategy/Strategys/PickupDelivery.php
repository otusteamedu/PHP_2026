<?php

namespace App\Strategy\Strategys;

use App\Strategy\Entitys\Order;
use App\Strategy\Interfaces\DeliveryInterface;
use App\Strategy\ObjectValues\Money;

class PickupDelivery implements DeliveryInterface
{
    public function getCost(Order $order): Money
    {
        return new Money(1);
    }
}
