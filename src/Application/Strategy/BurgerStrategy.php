<?php

declare(strict_types=1);

namespace App\Application\Strategy;

use App\Application\Factory\BurgerFactory;
use App\Domain\Strategy\CookingStrategyInterface;

final readonly class BurgerStrategy implements CookingStrategyInterface
{
    public function __construct(
        private BurgerFactory $factory
    ) {
    }

    public function getFactory(): BurgerFactory
    {
        return $this->factory;
    }
}
