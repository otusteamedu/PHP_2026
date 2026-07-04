<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class ValidationReportResult
{
    public function __construct(
        public int $total,
        public int $errors,
        public int $pdfSize
    ) {}
}