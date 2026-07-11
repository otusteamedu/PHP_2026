<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum JobStatus: string
{
    case QUEUED = 'QUEUED';
    case PROCESSING = 'PROCESSING';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
}