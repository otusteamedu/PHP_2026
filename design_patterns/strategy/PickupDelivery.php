<?php

declare (strict_types = 1);

class PickupDelivery implements DeliveryInterface
{
    public function calc(array $order): float
    {
        $price = $order['weight'] * $order['distance'] + $order['kv'];

        return (float) $price;
    }
}