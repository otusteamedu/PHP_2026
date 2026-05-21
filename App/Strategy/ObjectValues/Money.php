<?php

namespace App\Strategy\ObjectValues;

class Money
{
    private string $currency = '₽'; // принимаем платежи только в национальной валюте
    private readonly float $amount; // float просто проще
    public function __construct(float $amount)
    {
        if ($amount < 0) {
            throw new \Exception('Сумма не может быть отрицательной.');
        }
        $this->amount = $amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}