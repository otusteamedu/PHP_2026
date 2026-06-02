<?php

declare (strict_types = 1);

interface DeliveryInterface
{
    public function calc(array $order): float;
}