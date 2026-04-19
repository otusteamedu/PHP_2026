<?php

declare(strict_types=1);

namespace App\Decorator;

final class Pepper extends IngredientDecorator
{
    protected function ingredientName(): string
    {
        return 'перец';
    }

    protected function ingredientPrice(): float
    {
        return 15.0;
    }
}