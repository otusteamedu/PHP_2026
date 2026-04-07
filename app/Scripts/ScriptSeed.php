<?php

namespace App\Scripts;

use App\Repository\ElasticSearchRepository;

require __DIR__ . '/../../vendor/autoload.php';

$options = getopt("", ["index:"]);
if (!isset($options['index'])) echo 'Переайте название в виде --index="example"';
$index = $options['index'];

$filePath = __DIR__ . '/../Data/books.json';
if (!file_exists($filePath)) {
    echo "Файл не найден" . PHP_EOL;
}
$books = json_decode(file_get_contents($filePath), true);

$elasticSearchRepository = new ElasticSearchRepository;

$elasticSearchRepository->bulk($index, $books);
