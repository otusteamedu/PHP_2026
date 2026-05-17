<?php

declare(strict_types=1);

namespace App\Application\Factory;

use App\Domain\Entity\Sandwich;
use App\Domain\Factory\CreateProductInterface;

final readonly class SandwichFactory implements CreateProductInterface
{
    public function create(): Sandwich
    {
        return new Sandwich();
    }
}
