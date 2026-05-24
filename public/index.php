<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Kernel;

$kernel = new Kernel();
echo $kernel->handle();
