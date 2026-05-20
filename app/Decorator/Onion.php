<?php

declare(strict_types=1);

namespace App\Decorator;

final class Onion extends IngredientDecorator
{
    protected function ingredientName(): string
    {
        return 'лук';
    }

    protected function ingredientPrice(): float
    {
        return 10.0;
    }
}