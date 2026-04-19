<?php

declare(strict_types=1);

namespace App\Factory;

use App\Domain\Product\Burger;
use App\Domain\Product\ProductInterface;

final class BurgerFactory implements ProductFactoryInterface
{
    public function create(): ProductInterface
    {
        return new Burger();
    }
}