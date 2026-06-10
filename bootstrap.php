<?php

declare(strict_types=1);

/**
 * Composer autoloader bootstrap.
 *
 * Run `composer install` before starting the app (web or worker) so that
 * vendor/autoload.php exists and the App\ namespace is autoloaded.
 */
$autoload = __DIR__ . '/vendor/autoload.php';

if (!is_file($autoload)) {
    fwrite(STDERR, "vendor/autoload.php не найден — выполните: composer install" . PHP_EOL);
    exit(1);
}

require $autoload;