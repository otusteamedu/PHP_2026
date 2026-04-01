<?php

namespace App\Services;

interface EmailValidationInterface
{
    public function check(string $email): ValidationResult;
}