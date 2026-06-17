<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

class Book
{
    public function __construct(
        public string $sku,
        public string $title,
        public string $category,
        public int $price,
        public int $stockTotal,
    ) {}

    public static function fromArray(array $row): self
    {
        foreach (['sku', 'title', 'category', 'price'] as $key) {
            if (!isset($row[$key])) {
                throw new InvalidArgumentException("Missing {$key}");
            }
        }

        $stockTotal = array_sum(array_map(
            fn($s) => (int)($s['stock'] ?? 0),
            $row['stock'] ?? []
        ));

        return new self(
            sku: (string)$row['sku'],
            title: (string)$row['title'],
            category: (string)$row['category'],
            price: (int)$row['price'],
            stockTotal: $stockTotal,
        );
    }

    public function toElastic(): array
    {
        return [
            'sku' => $this->sku,
            'title' => $this->title,
            'category' => $this->category,            
            'price' => $this->price,
            'stock_total' => $this->stockTotal,
        ];
    }
}
