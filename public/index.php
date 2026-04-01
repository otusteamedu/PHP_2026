<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$handler = new \Aharutyunyan\Hw\Interfaces\Handler\EmailValidationHandler();
$result = $handler->handle();

echo '<pre>';
print_r($result);
echo '</pre>';