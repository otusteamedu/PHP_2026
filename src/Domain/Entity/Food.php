<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Enum\Status;
use App\Domain\ValueObject\Ingredient;
use SplObserver;
use SplPriorityQueue;
use SplSubject;

abstract class Food implements Cookable, SplSubject
{
    public Status $status {
        get {
            return $this->status;
        }
    }

    /**
     * @var SplPriorityQueue<int, Ingredient>
     */
    private SplPriorityQueue $ingredients;

    /**
     * @var SplObserver[]
     */
    private array $observers = [];

    public function __construct(
        private readonly string $name,
        ?SplPriorityQueue $ingredients = null,
    ) {
        $this->status = Status::Created;
        $this->ingredients = $ingredients ?? new SplPriorityQueue();

        foreach ($this->defaultIngredients() as $ingredient) {
            $this->ingredients->insert($ingredient, $ingredient->priority);
        }
    }

    /**
     * @return Ingredient[]
     */
    abstract protected function defaultIngredients(): array;

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Ingredient[]
     */
    public function cook(): array
    {
        $result = [];

        foreach ($this->ingredients as $ingredient) {
            $result[] = $ingredient;
        }

        $this->status = Status::Cooked;
        $this->notify();

        return $result;
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
