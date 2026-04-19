<?php

declare(strict_types=1);

namespace App\Factory;

use App\Domain\Product\HotDog;
use App\Domain\Product\ProductInterface;

final class HotDogFactory implements ProductFactoryInterface
{
    public function create(): ProductInterface
    {
        return new HotDog();
    }
}