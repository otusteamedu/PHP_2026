#!/usr/bin/env php
<?php

namespace App;

use App\Services\Cli\CliHelper;
use App\Services\Elastic\ElasticSearchService;
use App\Services\Preflight\Bootstrap;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

require __DIR__ . '/../vendor/autoload.php';

Bootstrap::preflight();

$processor = new CliHelper();
$processor->mapInputToArgs($argv);

$processedArgs = $processor->args;

//print_r($processedArgs);
//print_r($argv);

$isEmpty = count(array_filter($processedArgs)) === 0;

if ($isEmpty) {
    echo "\e[33mCLI Search Interface. Description: \n\n\e[0m";

    echo 'By using this CLI application, you can find information about books and wines in our collection.' . PHP_EOL . PHP_EOL;
    echo 'Don\'t forget to create an index before searching.'. PHP_EOL;
    echo 'You can do it with a special command from the list bellow'. PHP_EOL;

    echo PHP_EOL . 'Command list:' . PHP_EOL;

    echo '  -b - recreate book index' . PHP_EOL;
    echo '  -w - recreate wine index' . PHP_EOL . PHP_EOL;
    echo '  --search - type anything to search with Elastic' . PHP_EOL;
    echo '  --sort=min|max - sort results by price' . PHP_EOL;
    echo '  --stock-gt=10 - filter by stock > value' . PHP_EOL;
    echo '  --strict=0|1 - strict match (default 1)' . PHP_EOL;

    die();
}


try {
    $client = ClientBuilder::create()
            ->setHosts(['http://localhost:9200']) // Use service name from docker-compose
            ->build();
} catch (AuthenticationException $e) {

}

// Test connection
if ($client->ping()) {
    echo PHP_EOL . "Connected to Elasticsearch successfully!\n";
} else {
    die(PHP_EOL . "Failed to connect to Elasticsearch.");
}

$elastic = new ElasticSearchService($client);

if (isset($processedArgs['flags']['b']) || isset($processedArgs['flags']['w'])) {
    echo 'We will recreate index shortly...' . PHP_EOL;
    sleep(2);
    if (isset($processedArgs['flags']['w'])) {
        $elastic->createWineIndex();
    }
    if (isset($processedArgs['flags']['b'])) {
        $elastic->createBookIndex();
    }
    echo PHP_EOL . "\e[32mIndex was successfully recreated \n\n\e[0m";
}

if (isset($processedArgs['search'])) {
    $stockGt = null;
    if (is_numeric($processedArgs['stock_gt'])) {
        $stockGt = (int) $processedArgs['stock_gt'];
    }
    $strict = true;
    if ($processedArgs['strict'] !== null) {
        $strict = !in_array((string) $processedArgs['strict'], ['0', 'false', 'no'], true);
    }

    $results = $elastic->search(
        $processedArgs['search'],
        sort: is_string($processedArgs['sort']) ? $processedArgs['sort'] : null,
        stockGt: $stockGt,
        strict: $strict,
    );

    echo PHP_EOL . "\e[32mSearch results \n\n\e[0m";
    if ($results === []) {
        echo "Nothing found for: {$processedArgs['search']}" . PHP_EOL;
        exit(0);
    }

    foreach ($results as $result) {
        echo sprintf(
            '%s | %s | %s | score: %.3f',
            $result['title'],
            $result['category'],
            (string) $result['price'],
            (float) ($result['score'] ?? 0),
        ) . PHP_EOL;
    }
}
