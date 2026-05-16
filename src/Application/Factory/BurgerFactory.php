<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Entity\Burger;
use App\Domain\Factory\CreateProductInterface;

final readonly class BurgerFactory implements CreateProductInterface
{
    public function create(): Burger
    {
        return new Burger();
    }
}
