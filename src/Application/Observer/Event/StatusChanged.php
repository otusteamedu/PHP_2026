<?php

declare(strict_types=1);

namespace App\Application\Observer\Event;

use App\Domain\Enum\Status;
use App\Domain\Observer\Event\Event;

final readonly class StatusChanged extends Event
{
    public function __construct(
        public string $product,
        public Status $from,
        public Status $to,
    ) {
    }
}
