<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Conditions;
use App\Domain\ValueObject\Event;
use App\Domain\ValueObject\Priority;

final readonly class AnalyticEvent
{
    public function __construct(
        public Priority $priority,
        public Conditions $conditions,
        public Event $event,
    ) {
    }
}
