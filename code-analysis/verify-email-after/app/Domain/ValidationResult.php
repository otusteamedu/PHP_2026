<?php

declare(strict_types=1);

namespace App\Domain;
readonly class ValidationResult
{
    public function __construct(
        public bool   $isValid,
        public string|ValidationMessages $message = ''
    ) {
        $this->message = $message instanceof ValidationMessages ? $message->value : $message;
    }
}