<?php

declare (strict_types = 1);

class DeliveryCalculatorService
{
    public function __construct(private DeliveryInterface $service)
    {}

    public function set(DeliveryInterface $service): void
    {
        $this->service = $service;
    }

    public function calculate(array $order): float
    {
        return $this->service->calc($order);
    }
}