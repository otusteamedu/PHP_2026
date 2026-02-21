<?php

declare(strict_types=1);

namespace AHarutyunyan\Hw4;

use Slim\Factory\AppFactory;
use AHarutyunyan\Hw4\Controller\CheckParenthesisController;

class App
{
    public function run(): void
    {
        $app = AppFactory::create();

        $app->addBodyParsingMiddleware();
        $app->post('/', [CheckParenthesisController::class, 'check']);

        $app->run();
    }
}