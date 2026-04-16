<?php

declare(strict_types=1);

namespace App;

readonly class Stock
{
    public function __construct(
        public string $shop,
        public int $stock,

    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['shop'],
            $data['stock'],
        );
    }
}
