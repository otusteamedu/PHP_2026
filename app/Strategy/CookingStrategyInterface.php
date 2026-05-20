<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Domain\Product\ProductInterface;

interface CookingStrategyInterface
{
    /**
     * Создаёт базовый продукт и применяет ингредиенты по рецепту.
     */
    public function prepare(): ProductInterface;
}