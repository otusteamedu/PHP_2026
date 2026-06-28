<?php

declare (strict_types = 1);

namespace App\Domain;

final readonly class ValidationRequest
{
    public function __construct(
        public array $emails,
        public string $reportEmail,
    ) {}
}
