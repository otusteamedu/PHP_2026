<?php
// php app/Scripts/ScriptDeleteIndex.php --index="example"
namespace App\Scripts;

use App\Repository\ElasticSearchRepository;

require __DIR__ . '/../../vendor/autoload.php';

$options = getopt("", ["index:"]);
if (!isset($options['index'])) echo 'Переайте название в виде --index="example"';
$index = $options['index'];

$elasticSearchRepository = new ElasticSearchRepository;

$elasticSearchRepository->deleteIndex($index);
