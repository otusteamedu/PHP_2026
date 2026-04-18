#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\ElasticsearchClientFactory;
use App\OtusShopRepository;
use App\SearchInputParser;

require __DIR__ . '/../vendor/autoload.php';

try {
    $input = new SearchInputParser()->parse(array_slice($argv, 1));
    $repository = new OtusShopRepository(ElasticsearchClientFactory::create());
    $books = $repository->search($input);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}

$payload = [
    'input' => [
        'query' => $input->query,
        'category' => $input->category,
        'max_price' => $input->maxPrice,
        'in_stock' => $input->inStock,
    ],
    'count' => count($books),
    'items' => array_map(
        static fn ($book) => [
            'title' => $book->title,
            'sku' => $book->sku,
            'category' => $book->category,
            'price' => $book->price,
            'stock' => array_map(
                static fn ($stock) => [
                    'shop' => $stock->shop,
                    'stock' => $stock->stock,
                ],
                $book->stock,
            ),
        ],
        $books,
    ),
];

fwrite(STDOUT, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL);
