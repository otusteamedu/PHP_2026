<?php

declare(strict_types=1);

use App\Infrastructure\Http\Action\HomeAction;
use App\Infrastructure\Http\Action\OpenApi\DocsAction;
use App\Infrastructure\Http\Action\OpenApi\SpecAction;
use App\Infrastructure\Http\Action\Job\CreateJobAction;
use App\Infrastructure\Http\Action\Job\GetJobStatusAction;
use Slim\App;

return static function (App $app): void {
    $app->get('/', HomeAction::class);
    $app->get('/openapi.json', SpecAction::class);
    $app->get('/docs', DocsAction::class);
    $app->post('/job', CreateJobAction::class);
    $app->get('/job/{id}', GetJobStatusAction::class);
};
