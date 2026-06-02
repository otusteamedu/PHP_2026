<?php

declare (strict_types = 1);

class PostDelivery implements DeliveryInterface
{
    public function calc(array $order): float
    {
        $price = ($order['weight'] * $order['tariff']) + $order['kv'];

        return (float) $price;
    }
}