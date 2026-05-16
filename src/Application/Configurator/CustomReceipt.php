<?php

declare(strict_types=1);

namespace App\Application\Configurator;

use App\Domain\Configurator\ProductConfiguratorInterface;
use App\Domain\Entity\Cookable;
use App\Domain\Enum\Ingredient;
use ArrayObject;

final readonly class CustomReceipt implements ProductConfiguratorInterface
{
    /** @param ArrayObject<Ingredient> $ingredients */
    public function __construct(
        private ArrayObject $ingredients
    ) {
    }

    public function configure(Cookable $product): Cookable
    {
        foreach ($this->ingredients as $ingredient) {
            $product = $ingredient->getDecorator($product);
        }
        return $product;
    }
}
