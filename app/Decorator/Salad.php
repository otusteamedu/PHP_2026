<?php

declare(strict_types=1);

namespace App\Decorator;

final class Salad extends IngredientDecorator
{
    protected function ingredientName(): string
    {
        return 'салат';
    }

    protected function ingredientPrice(): float
    {
        return 20.0;
    }
}