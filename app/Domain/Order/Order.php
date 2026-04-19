<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Decorator\IngredientDecorator;
use App\Domain\Product\ProductInterface;
use App\Strategy\CookingStrategyInterface;

final class Order
{
    /**
     * @param list<class-string<IngredientDecorator>> $extras пожелания клиента
     */
    public function __construct(
        private readonly CookingStrategyInterface $strategy,
        private readonly array $extras = [],
    ) {
    }

    public function compose(): ProductInterface
    {
        $product = $this->strategy->prepare();

        foreach ($this->extras as $extraClass) {
            $product = new $extraClass($product);
        }

        return $product;
    }
}