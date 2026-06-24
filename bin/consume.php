#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Infrastructure\MessageBroker\Worker\Consumer;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';

/** @var Container $container */
$container = require __DIR__ . '/../config/container.php';

$container->get(Consumer::class)->consume();
