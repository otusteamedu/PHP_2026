<?php

declare(strict_types=1);

namespace App\Application\Strategy;

use App\Application\Factory\HotDogFactory;
use App\Domain\Strategy\CookingStrategyInterface;

final readonly class HotDogStrategy implements CookingStrategyInterface
{
    public function __construct(
        private HotDogFactory $factory
    ) {
    }

    public function getFactory(): HotDogFactory
    {
        return $this->factory;
    }
}
