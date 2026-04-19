<?php

declare(strict_types=1);

namespace App\Domain\Product;

abstract class BaseProduct implements ProductInterface
{
    public function __construct(
        private readonly string $name,
        /** @var list<string> */
        private readonly array $ingredients,
        private readonly float $price,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}