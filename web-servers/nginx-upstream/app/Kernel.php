<?php

declare(strict_types=1);

namespace App;

use App\Controllers\BracketController;
use App\Controllers\RedisController;

class Kernel
{
    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        match ($method) {
            'POST' => (new BracketController())->handle(),
            'GET'  => (new RedisController())->handle(),
            default => http_response_code(405),
        };
    }
}
