<?php

declare(strict_types=1);

namespace App\Application\Observer;

use App\Application\Observer\Event\StatusChanged;
use App\Domain\Enum\Status;
use App\Domain\Observer\ObserverInterface;

/**
 * @implements ObserverInterface<StatusChanged>
 */
final readonly class NotificationSender implements ObserverInterface
{
    public function update($event): void
    {
        $message = match ($event->to) {
            Status::COOKING => "{$event->product} готовится",
            Status::DONE => "{$event->product} готов",
            Status::FAILED => "{$event->product} забракован",
            default => '',
        };

        if (!empty($message)) {
            echo $message . PHP_EOL;
        }
    }
}
