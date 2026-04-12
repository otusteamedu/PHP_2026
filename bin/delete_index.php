#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\OtusShopRepository;
use Elastic\Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()
        ->setHosts(['http://elastic:9200'])
        ->build();

$shopRepository = new OtusShopRepository($client);

try {
    $shopRepository->deleteIndex();
} catch (Exception $exception) {
    echo $exception->getMessage();
    exit(1);
}

echo 'Индекс успешно удален!' . PHP_EOL;
exit(0);
