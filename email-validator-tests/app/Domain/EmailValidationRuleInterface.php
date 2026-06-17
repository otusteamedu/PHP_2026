<?php

declare(strict_types=1);

namespace App\Domain;
interface EmailValidationRuleInterface
{
    public function check(string $email): ValidationResult;
}