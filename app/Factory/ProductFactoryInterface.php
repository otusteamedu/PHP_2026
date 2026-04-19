<?php

declare(strict_types=1);

namespace App\Factory;

use App\Domain\Product\ProductInterface;

interface ProductFactoryInterface
{
    public function create(): ProductInterface;
}