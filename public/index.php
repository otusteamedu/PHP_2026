<?php

declare(strict_types=1);

use App\Controller\ReportRequestController;
use Slim\Factory\AppFactory;

require __DIR__ . '/../src/bootstrap.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$controller = new ReportRequestController();

$app->post('/api/report-request', [$controller, 'create']);

$app->run();
