<?php

namespace App\Domain\Task;

use App\Domain\Task\ValueObjects\TaskDescription;
use App\Domain\Task\ValueObjects\TaskTitle;
use DateTimeImmutable;

final class TaskAggregate
{
    private function __construct(
        private ?int $id,
        private int $ownerUserId,
        private TaskTitle $title,
        private ?TaskDescription $description,
        private bool $isDone,
        private ?DateTimeImmutable $dueAt,
    ) {}

    public static function begin(
        int $ownerUserId,
        TaskTitle $title,
        ?TaskDescription $description,
        bool $initialDone,
        ?DateTimeImmutable $dueAt,
    ): self {
        if ($ownerUserId < 1) {
            throw new \InvalidArgumentException('owner');
        }

        return new self(null, $ownerUserId, $title, $description, $initialDone, $dueAt);
    }

    public static function restore(
        int $id,
        int $ownerUserId,
        TaskTitle $title,
        ?TaskDescription $description,
        bool $isDone,
        ?DateTimeImmutable $dueAt,
    ): self {
        if ($id < 1 || $ownerUserId < 1) {
            throw new \InvalidArgumentException('ids');
        }

        return new self($id, $ownerUserId, $title, $description, $isDone, $dueAt);
    }

    public function assignPersistedIdentity(int $id): void
    {
        if ($this->id !== null) {
            throw new \LogicException('id');
        }
        if ($id < 1) {
            throw new \InvalidArgumentException('id');
        }
        $this->id = $id;
    }

    public function recordTitleNotesAndSchedule(
        TaskTitle $title,
        ?TaskDescription $description,
        bool $isDone,
        ?DateTimeImmutable $dueAt,
    ): void {
        $this->title = $title;
        $this->description = $description;
        $this->isDone = $isDone;
        $this->dueAt = $dueAt;
    }

    public function markCompleted(): void
    {
        $this->isDone = true;
    }

    public function markIncomplete(): void
    {
        $this->isDone = false;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function ownerUserId(): int
    {
        return $this->ownerUserId;
    }

    public function title(): TaskTitle
    {
        return $this->title;
    }

    public function description(): ?TaskDescription
    {
        return $this->description;
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function dueAt(): ?DateTimeImmutable
    {
        return $this->dueAt;
    }
}
