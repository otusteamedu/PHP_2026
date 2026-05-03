<?php
declare(strict_types=1);

namespace App\Domain;

class SearchDTO
{
    public function __construct(
        public ?string $query = null,
        public ?string $category = null,
        public ?int $maxPrice = null,
        public bool $inStock = false,
        public int $limit = 10,
    ) {}
}