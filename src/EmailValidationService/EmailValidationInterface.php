<?php

namespace App\EmailValidationService;

interface EmailValidationInterface
{
    public static function checkEmail(string $email): bool;
}
