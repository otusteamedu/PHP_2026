#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\OtusShopRepository;
use Elastic\Elasticsearch\ClientBuilder;

require __DIR__ . '/../vendor/autoload.php';

$client = ClientBuilder::create()->setHosts(['http://elastic:9200'])->build();

$shopRepository = new OtusShopRepository($client);

try {
    $shopRepository->createIndex();
    $shopRepository->fillIndex();
} catch (Exception $exception) {
    echo $exception->getMessage();
    exit(1);
}

echo 'Индекс успешно создан и заполнен!' . PHP_EOL;
exit(0);
