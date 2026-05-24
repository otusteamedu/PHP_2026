<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Facades\Views;
use App\Services\EmailValidationService;

class EmailController
{
    public function index(): void
    {
        Views::view('emails-validator/index');
    }

    public function check(): void
    {        
        (new EmailValidationService())->validate();
    }
}
