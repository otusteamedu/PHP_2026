<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

abstract class Ingredient
{
    public function __construct(
        public int $priority = 0,
    )
    {
    }

    abstract public function prepare(): string;
}
