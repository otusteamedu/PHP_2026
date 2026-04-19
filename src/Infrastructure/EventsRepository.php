<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Dto\Event;

final readonly class EventsRepository
{
    public function __construct(private Storage $client)
    {}

    public function set(Event $event): void
    {

    }

    public function get(array $params): Event
    {

    }
}
