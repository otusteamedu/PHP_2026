<?php

declare(strict_types=1);

namespace App\Application\Ports;

use App\Domain\Entity\EmailValidationJob;
use App\Domain\Enum\JobStatus;

interface JobRepositoryInterface
{
    public function create(EmailValidationJob $job): int;

    public function find(int $id): ?EmailValidationJob;

    public function updateStatus(int $id, JobStatus $status): void;
}