<?php

require __DIR__.'/../vendor/autoload.php';

use App\Kernel;
use Dotenv\Dotenv;

define('BASE_PATH', dirname(__DIR__));

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

(new Kernel())->run();
