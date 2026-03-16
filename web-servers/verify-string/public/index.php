<?php

require __DIR__.'/../vendor/autoload.php';

use App\Kernel;

echo (new Kernel())->run();
