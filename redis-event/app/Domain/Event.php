<?php

declare (strict_types = 1);

namespace App\Domain;

use InvalidArgumentException;

final readonly class Event
{
    public string $id;
    public int $priority;
    public array $conditions;
    public array $eventData;

    public function __construct(
        string $id,
        int $priority,
        array $conditions,
        array $eventData = []
    ) {
        if ($id === '') {
            throw new InvalidArgumentException('Отсутствует событие');
        }

        $this->id         = $id;
        $this->priority   = $priority;
        $this->conditions = $conditions;
        $this->eventData  = $eventData;
    }

    public static function create(int $priority, array $conditions, array $eventData = []): self
    {
        return new self(
            bin2hex(random_bytes(8)),
            $priority,
            $conditions,
            $eventData
        );
    }

    public static function getEventfromStorage(array $data): self
    {
        return new self(
            (string) $data['id'],
            (int) $data['priority'],
            is_array($data['conditions']) ? $data['conditions'] : [],
            is_array($data['eventData']) ? $data['eventData'] : []
        );
    }

    public function matches(array $params): bool
    {
        $intersection = array_intersect_assoc($this->conditions, $params);
        return count($intersection) === count($this->conditions);
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'priority'   => $this->priority,
            'conditions' => $this->conditions,
            'eventData'  => $this->eventData,
        ];
    }
}
