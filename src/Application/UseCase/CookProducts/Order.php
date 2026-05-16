<?php

declare(strict_types=1);

namespace App\Application\UseCase\CookProducts;

use ArrayObject;

final readonly class Order
{
    /** @param ArrayObject<Position> $positions */
    public function __construct(
        public ArrayObject $positions,
    ) {
    }
}
