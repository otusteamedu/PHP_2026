<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

use App\Domain\EmailValidationRuleInterface;
use App\Domain\ValidationResult;
use App\Domain\ValidationMessages;

class EmailValidation implements EmailValidationRuleInterface
{
    public function check(string $email): ValidationResult
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new ValidationResult(false, ValidationMessages::INVALID_FORMAT->value);
        }

        return new ValidationResult(true);
    }
}