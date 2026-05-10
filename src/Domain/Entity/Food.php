<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\Status;
use SplObserver;
use SplSubject;

abstract class Food implements Cookable, SplSubject
{
    public Status $status {
        get {
            return $this->status;
        }
    }

    /**
     * @var SplObserver[]
     */
    private array $observers = [];

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

    public function attach(SplObserver $observer): void
    {
        $this->observers[spl_object_hash($observer)] = $observer;
    }

    public function detach(SplObserver $observer): void
    {
        unset($this->observers[spl_object_hash($observer)]);
    }

    public function notify(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}
