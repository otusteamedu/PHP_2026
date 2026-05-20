<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Decorator\Cheese;
use App\Decorator\Salad;
use App\Domain\Product\ProductInterface;
use App\Factory\BurgerFactory;

final readonly class BurgerStrategy implements CookingStrategyInterface
{
    public function __construct(private BurgerFactory $factory)
    {
    }

    public function prepare(): ProductInterface
    {
        $product = $this->factory->create();
        $product = new Cheese($product);
        return new Salad($product);
    }
}