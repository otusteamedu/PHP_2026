<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\Container\Container;

class Kernel
{
    public function run(): void
    {
        $container = new Container();

        $controller = $container->emailController();

        match ($_SERVER['REQUEST_METHOD']) {
            'GET'  => $controller->index(),
            'POST' => $controller->check(),
            default => http_response_code(405),
        };
    }
}
