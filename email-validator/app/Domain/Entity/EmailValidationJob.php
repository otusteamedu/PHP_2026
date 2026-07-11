<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\JobStatus;
use DateTimeImmutable;

class EmailValidationJob
{
    public function __construct(
        public ?int $id,
        public JobStatus $status,
        public readonly array $emails,
        public readonly string $reportEmail,
        public readonly DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt
    ) {}

    public function changeStatus(JobStatus $newStatus): void
    {
        $this->status = $newStatus;
        $this->updatedAt = new DateTimeImmutable();
    }
}