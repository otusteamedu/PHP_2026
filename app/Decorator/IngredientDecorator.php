<?php

declare(strict_types=1);

namespace App\Decorator;

use App\Domain\Product\ProductInterface;

abstract class IngredientDecorator implements ProductInterface
{
    public function __construct(protected readonly ProductInterface $product)
    {
    }

    abstract protected function ingredientName(): string;

    abstract protected function ingredientPrice(): float;

    public function getName(): string
    {
        return $this->product->getName();
    }

    public function getIngredients(): array
    {
        return [...$this->product->getIngredients(), $this->ingredientName()];
    }

    public function getPrice(): float
    {
        return $this->product->getPrice() + $this->ingredientPrice();
    }
}