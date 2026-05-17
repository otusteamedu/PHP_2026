<?php

declare(strict_types=1);

namespace App\Application\UseCase\CookProducts;

use App\Domain\Configurator\ProductConfiguratorInterface;

final readonly class Position
{
    public function __construct(
        public string $product,
        public ProductConfiguratorInterface $configurator,
    ) {
    }
}
