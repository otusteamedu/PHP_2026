#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\ElasticsearchClientFactory;
use App\OtusShopRepository;

require __DIR__ . '/../vendor/autoload.php';

$shopRepository = new OtusShopRepository(ElasticsearchClientFactory::create());

try {
    $shopRepository->deleteIndex();
} catch (Exception $exception) {
    echo $exception->getMessage();
    exit(1);
}

echo 'Индекс успешно удален!' . PHP_EOL;
exit(0);
