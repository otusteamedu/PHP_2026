<?php

declare(strict_types=1);

namespace App\Services;

class EmailValidation implements EmailValidationInterface
{
    public function check(string $email): ValidationResult
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new ValidationResult(false, 'Не корректный адрес электронной почты');
        }

        return new ValidationResult(true);
    }
}