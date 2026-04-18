#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\ElasticsearchClientFactory;
use App\OtusShopRepository;
use App\ResultTableRenderer;
use App\SearchInputParser;

require __DIR__ . '/../vendor/autoload.php';

try {
    $input = new SearchInputParser()->parse(array_slice($argv, 1));
    $repository = new OtusShopRepository(ElasticsearchClientFactory::create());
    $books = $repository->search($input);
    new ResultTableRenderer()->render($books);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
