<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class Book
{
    /**
     * @param Stock[] $stock
     */
    public function __construct(
        public string $title,
        public string $sku,
        public string $category,
        public float $price,
        public array $stock,
    ) {}

    public static function fromArray(array $data): self
    {
        $stock = $data['stock'] ?: [];
        return new self(
            $data['title'] ?? '',
            $data['sku'] ?? '',
            $data['category'] ?? '',
            $data['price'] ?? 0,
            array_map(fn (array $item) => Stock::fromArray($item), $stock),
        );
    }
}
