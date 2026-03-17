<?php

use App\app\Http\Controllers\CheckRandomStringController;
use App\app\Services\SessionService;
use App\app\Services\Validation\ParenthesesValidator;

SessionService::start();

echo new CheckRandomStringController(
    new ParenthesesValidator('/^[()]+$/')
)->execute($_POST['string'] ?? null);
