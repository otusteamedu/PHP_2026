<?php

declare(strict_types=1);

namespace App\Proxy;

use App\Domain\Product\ProductInterface;

interface CookInterface
{
    /**
     * Готовит продукт. Возвращает null, если продукт был утилизирован.
     */
    public function cook(ProductInterface $product): ?ProductInterface;
}