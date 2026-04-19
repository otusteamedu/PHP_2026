<?php

declare(strict_types=1);

namespace App\Strategy;

use App\Decorator\Onion;
use App\Domain\Product\ProductInterface;
use App\Factory\HotDogFactory;

final readonly class HotDogStrategy implements CookingStrategyInterface
{
    public function __construct(private HotDogFactory $factory)
    {
    }

    public function prepare(): ProductInterface
    {
        $product = $this->factory->create();
        $product = new Onion($product);

        return $product;
    }
}