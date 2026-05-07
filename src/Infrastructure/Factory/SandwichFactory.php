<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Entity\Sandwich;
use App\Domain\Factory\FoodFactoryInterface;

final class SandwichFactory implements FoodFactoryInterface
{
    public function create(string $name): Sandwich
    {
        return new Sandwich($name);
    }
}
