<?php

require __DIR__.'/../vendor/autoload.php';

use App\Kernel;

define('BASE_PATH', dirname(__DIR__));

(new Kernel())->run();
