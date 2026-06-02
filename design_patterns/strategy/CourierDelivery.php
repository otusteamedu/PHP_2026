<?php

declare (strict_types = 1);

class CourierDelivery implements DeliveryInterface
{
    public function calc(array $order): float
    {
        $price = $order['quantity'] * $order['ratio'];

        return (float) $price;
    }
}