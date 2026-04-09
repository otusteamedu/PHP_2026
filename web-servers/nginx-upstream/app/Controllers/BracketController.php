<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\BracketService;

class BracketController
{
    public function handle(): void
    {
        (new BracketService())->run();
    }
}