<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Template\Importers\CsvImporter;
use App\Template\Importers\JsonImporter;
use App\Template\Importers\XmlImporter;


$importer = new CsvImporter(__DIR__ . '/example.csv');
$importer->import();

$importer = new JsonImporter(__DIR__ . '/example.json');
$importer->import();

$importer = new XmlImporter(__DIR__ . '/example.xml');
$importer->import();
