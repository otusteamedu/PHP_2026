<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\Status;

abstract class Food implements Cookable
{
    public Status $status {
        get {
            return $this->status;
        }
    }

    public function __construct(
        private string $name
    ) {
        $this->status = Status::Created;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function cook(): void
    {
        $this->status = Status::Cooked;
    }
}
