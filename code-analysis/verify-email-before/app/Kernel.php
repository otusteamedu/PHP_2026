<?php

declare(strict_types=1);

namespace App;

use App\Controllers\EmailController;

class Kernel
{
    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $controller = new EmailController();

        match ($method) {
            'POST' => $controller->check(),
            'GET'  => $controller->index(),
            default => http_response_code(405),
        };
    }
}
