#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\ElasticsearchClientFactory;
use App\OtusShopRepository;

require __DIR__ . '/../vendor/autoload.php';

$shopRepository = new OtusShopRepository(ElasticsearchClientFactory::create());

try {
    $shopRepository->createIndex();
    $shopRepository->fillIndex();
} catch (Exception $exception) {
    echo $exception->getMessage();
    exit(1);
}

echo 'Индекс успешно создан и заполнен!' . PHP_EOL;
exit(0);
