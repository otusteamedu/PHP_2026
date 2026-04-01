<?php

declare(strict_types=1);

namespace App\Services;

class EmailValidatorFabric {

    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function validate(string $email): ValidationResult
    {
        foreach ($this->rules as $rule) {
            $result = $rule->check($email);

            if (!$result->isValid) {
                return $result;
            }
        }

        return new ValidationResult(true);
    }
}