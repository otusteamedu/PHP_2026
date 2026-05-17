<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Entity\HotDog;
use App\Domain\Factory\CreateProductInterface;

final readonly class HotDogFactory implements CreateProductInterface
{
    public function create(): HotDog
    {
        return new HotDog();
    }
}
