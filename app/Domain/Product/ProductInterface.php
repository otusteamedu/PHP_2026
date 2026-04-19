<?php

declare(strict_types=1);

namespace App\Domain\Product;

interface ProductInterface
{
    public function getName(): string;

    /** @return list<string> */
    public function getIngredients(): array;

    public function getPrice(): float;
}