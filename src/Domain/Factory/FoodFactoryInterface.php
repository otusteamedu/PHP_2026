<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Entity\Food;

interface FoodFactoryInterface
{
    public function create(string $name): Food;
}
