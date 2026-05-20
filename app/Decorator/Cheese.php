<?php

declare(strict_types=1);

namespace App\Decorator;

final class Cheese extends IngredientDecorator
{
    protected function ingredientName(): string
    {
        return 'сыр';
    }

    protected function ingredientPrice(): float
    {
        return 30.0;
    }
}