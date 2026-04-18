<?php

declare(strict_types=1);

namespace App;

use Webmozart\Assert\Assert;

final readonly class SearchInput
{
    private function __construct(
        public ?string $query,
        public ?string $category,
        public ?int $minPrice,
        public ?int $maxPrice,
        public bool $inStock,
    ) {}

    public static function create(array $data): self
    {
        $query = $data['--query'] ?? null;
        $category = $data['--category'] ?? null;
        $minPrice = $data['--min-price'] ? (int) $data['--min-price'] : null;
        $maxPrice = $data['--max-price'] ? (int) $data['--max-price'] : null;
        $inStock = $data['--in-stock'] ?? false;

        Assert::nullOrString($query, 'Параметр --query должен быть строкой.');
        Assert::nullOrString($category, 'Параметр --category должен быть строкой.');
        Assert::nullOrPositiveInteger($minPrice, 'Минимальная цена должна быть положительным числом.');
        Assert::nullOrPositiveInteger($maxPrice, 'Максимальная цена должна быть положительным числом.');
        Assert::boolean($inStock, 'Параметр --in-stock должен быть bool.');

        return new self($query, $category, $minPrice, $maxPrice, $inStock);
    }
}
