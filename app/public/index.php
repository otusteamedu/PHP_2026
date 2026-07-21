<?php

declare(strict_types=1);

use App\Controller\StatementController;
use App\Middleware\StatementSwagger;
use Slim\Factory\AppFactory;

require __DIR__ . '/../src/bootstrap.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->add(new StatementSwagger(
    $app,
    'hw21 Statement API',
    '1.0.0',
    'API для создания запросов на получение выписки и проверки статуса обработки.',
));

$controller = new StatementController();

$app->group('/api', function ($app) use ($controller): void {
    $app->post('/statements', [$controller, 'create']);
    $app->get('/statements/{id}', [$controller, 'getStatus']);
});

$app->run();
