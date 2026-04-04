#!/usr/bin/env php
<?php

declare(strict_types=1);

include __DIR__ . '/../src/ElasticClient.php';

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Script is allowed from cli only." . PHP_EOL);
    exit(1);
}

$client = new ElasticClient();

$result = $client->post();

var_dump($result);

exit(0);
