<?php

declare(strict_types=1);

namespace App\Domain\Product;

final class Burger extends BaseProduct
{
    public function __construct()
    {
        parent::__construct(
            name: 'Бургер',
            ingredients: ['булка', 'котлета', 'соус'],
            price: 250.0,
        );
    }
}