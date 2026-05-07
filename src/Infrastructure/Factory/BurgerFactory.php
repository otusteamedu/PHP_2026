<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Entity\Burger;
use App\Domain\Factory\FoodFactoryInterface;

final class BurgerFactory implements FoodFactoryInterface
{
    public function create(string $name): Burger
    {
        return new Burger($name);
    }
}
