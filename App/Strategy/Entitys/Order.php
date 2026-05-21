<?php

namespace App\Strategy\Entitys;

class Order
{
    public function __construct(
        private readonly string $customer,
        private readonly string $address,
        private readonly float $weight
    ) {}

    public function getCustomer(): string
    {
        return $this->customer;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }
}