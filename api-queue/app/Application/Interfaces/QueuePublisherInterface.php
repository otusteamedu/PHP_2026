<?php

declare (strict_types = 1);

namespace App\Application\Interfaces;

use App\Application\DTO\ValidationJobPayload;

interface QueuePublisherInterface
{
    public function publish(ValidationJobPayload $payload): void;
}
