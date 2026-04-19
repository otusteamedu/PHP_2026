<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Decorator\Cheese;
use App\Domain\Product\ProductInterface;
use App\Factory\SandwichFactory;

final readonly class SandwichStrategy implements CookingStrategyInterface
{
    public function __construct(private SandwichFactory $factory)
    {
    }

    public function prepare(): ProductInterface
    {
        $product = $this->factory->create();
        $product = new Cheese($product);

        return $product;
    }
}