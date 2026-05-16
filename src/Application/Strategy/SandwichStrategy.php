<?php

declare(strict_types=1);

namespace App\Application\Strategy;

use App\Application\Factory\SandwichFactory;
use App\Domain\Strategy\CookingStrategyInterface;

final readonly class SandwichStrategy implements CookingStrategyInterface
{
    public function __construct(
        private SandwichFactory $factory
    ) {
    }

    public function getFactory(): SandwichFactory
    {
        return $this->factory;
    }
}
