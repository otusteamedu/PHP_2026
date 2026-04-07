<?php

namespace App\Scripts;

use App\Repository\ElasticSearchRepository;
use App\Formatter\Formatter;

require __DIR__ . '/../../vendor/autoload.php';

$options = getopt("", ["index:", "search:"]);

if (!isset($options['index'])) echo 'Передайте название индекса в виде --index="example"' . PHP_EOL;
$index = $options['index'];
if (!isset($options['search'])) echo 'Передайте название книги в виде --search="рыцари"' . PHP_EOL;
$search = $options['search'];

$elasticSearchRepository = new ElasticSearchRepository;

$result = $elasticSearchRepository->search($index, $search);
echo Formatter::render($result);
