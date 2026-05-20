<?php

declare(strict_types=1);

namespace App\Domain\Product;

final class Sandwich extends BaseProduct
{
    public function __construct()
    {
        parent::__construct(
            name: 'Сэндвич',
            ingredients: ['тост', 'ветчина', 'масло'],
            price: 180.0,
        );
    }
}