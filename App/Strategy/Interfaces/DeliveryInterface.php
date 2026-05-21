<?php

namespace App\Strategy\Interfaces;

use App\Strategy\Entitys\Order;
use App\Strategy\ObjectValues\Money;

interface DeliveryInterface
{
    public function getCost(Order $order): Money;
}