<?php

declare(strict_types=1);

namespace App\Domain\Product;

final class HotDog extends BaseProduct
{
    public function __construct()
    {
        parent::__construct(
            name: 'Хот-дог',
            ingredients: ['булочка', 'сосиска', 'горчица'],
            price: 150.0,
        );
    }
}