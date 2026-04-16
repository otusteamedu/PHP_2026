#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\OtusShopRepository;
use Elastic\Elasticsearch\ClientBuilder;

require __DIR__ . '/../vendor/autoload.php';

$client = ClientBuilder::create()->setHosts(['http://elastic:9200'])->build();

$shopRepository = new OtusShopRepository($client);

try {
    var_dump($shopRepository->search('f'));
} catch (Exception $exception) {
    echo $exception->getMessage();
    exit(1);
}

exit(0);
