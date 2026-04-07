<?php

namespace App\Models;

use App\Core\Request;

class Event
{
    public function __construct(
        public int $priority,
        public array $conditions,
        public array $event
    ) {}

    public function toArray(): array
    {
        return [
            'priority' => $this->priority,
            'conditions' => $this->conditions,
            'event' => $this->event,
        ];
    }

    public static function fromRequest(Request $request): Event
    {
        $data = $request->getJsonBody();
        return new Event($data['priority'], $data['conditions'], $data['event']);
    }
}
