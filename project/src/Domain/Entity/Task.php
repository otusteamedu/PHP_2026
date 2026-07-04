<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\Status;
use App\Domain\ValueObject\Id;
use DomainException;

final class Task
{
    public function __construct(
        public readonly Id $id,
        private Status $status,
    ) {
    }

    public static function new(): self
    {
        return new self(
            Id::generate(),
            Status::Created
        );
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function start(): void
    {
        if ($this->status !== Status::Created) {
            throw new DomainException('Статус задаче не допустим для обработки');
        }
        $this->status = Status::InProgress;
    }

    public function finish(): void
    {
        if ($this->status !== Status::InProgress) {
            throw new DomainException('Статус не допустим для завершения');
        }
        $this->status = Status::Completed;
    }

    public function cancel(): void
    {
        $this->status = Status::Cancelled;
    }
}
