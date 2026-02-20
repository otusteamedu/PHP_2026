<?php

declare(strict_types=1);

namespace AHarutyunyan\Hw4;

use Slim\Factory\AppFactory;
use AHarutyunyan\Hw4\Controller\CheckParenthesisController;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->post('/', [CheckParenthesisController::class, 'check']);

return $app;