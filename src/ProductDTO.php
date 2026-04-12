<?php

declare(strict_types=1);

namespace App;

readonly class Book
{
    public function __construct(
        public string $title,
        public string $sku,
        public string $category,
        public int $price,
        public array $stock,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            sku: $data['sku'],
            category: $data['category'],
            price: $data['price'],
            stock: $data['stock'],
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'sku' => $this->sku,
            'category' => $this->category,
            'price' => $this->price,
            'stock' => $this->stock,
        ];
    }
}
