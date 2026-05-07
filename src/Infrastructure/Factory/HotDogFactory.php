<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Entity\HotDog;
use App\Domain\Factory\FoodFactoryInterface;

final class HotDogFactory implements FoodFactoryInterface
{
    public function create(string $name): HotDog
    {
        return new HotDog($name);
    }
}
