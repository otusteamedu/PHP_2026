#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\ElasticClient;

include __DIR__ . '/../src/ElasticClient.php';

$json = file_get_contents(__DIR__ . '/../otus-shop.json');
$result = new ElasticClient()->put(ElasticClient::ACTION_OTUS_SHOP, $json);


var_dump($result);

exit(0);
