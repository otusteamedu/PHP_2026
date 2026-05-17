<?php

declare(strict_types=1);

namespace App\Application\UseCase\CookProducts;

final readonly class Response
{
    /** @param string[] $resultProducts */
    public function __construct(
        public array $resultProducts
    ) {
    }
}
