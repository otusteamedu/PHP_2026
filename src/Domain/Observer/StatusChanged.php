<?php

declare(strict_types=1);

namespace App\Domain\Observer;

use App\Domain\Enum\Status;

final readonly class StatusChanged extends Event
{
    public function __construct(
        public string $product,
        public Status $from,
        public Status $to,
    ) {
    }
}
