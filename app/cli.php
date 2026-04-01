<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Search\BookRepository;
use App\Formatter\TableFormatter;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

$opts = getopt('', ['query:', 'max-price:', 'min-stock:']);
$query = $opts['query'] ?? '';
$maxPrice = (float)($opts['max-price'] ?? PHP_INT_MAX);
$minStock = (int)($opts['min-stock'] ?? 1);

if ($query === '') {
    fwrite(STDERR, "Использование: php app/cli.php --query=\"текст\" [--max-price=2000] [--min-stock=1]\n");
    exit(1);
}

$host = getenv('ES_HOST');
$index = getenv('ES_INDEX');

try {
    $repo = new BookRepository($host, $index);
    $books = $repo->search($query, $maxPrice, $minStock);

    $formatter = new TableFormatter();
    echo $formatter->render($books);
} catch (ClientResponseException|ServerResponseException|AuthenticationException $e) {
    throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
}
