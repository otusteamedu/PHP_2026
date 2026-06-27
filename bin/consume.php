#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Infrastructure\RabbitMq\Worker\Consumer;

require __DIR__ . '/../vendor/autoload.php';

Consumer::create()->consume();
