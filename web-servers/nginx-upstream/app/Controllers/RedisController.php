<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\RedisPageService;

class RedisController
{
    public function handle(): void
    {
        (new RedisPageService())->render();
    }
}