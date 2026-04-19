<?php

declare(strict_types=1);

namespace App\Factory;

use App\Domain\Product\ProductInterface;
use App\Domain\Product\Sandwich;

final class SandwichFactory implements ProductFactoryInterface
{
    public function create(): ProductInterface
    {
        return new Sandwich();
    }
}