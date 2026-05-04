<?php

declare(strict_types=1);

namespace App\DataMapper;

use ArrayIterator;
use ArrayObject;
use InvalidArgumentException;

/**
 * @template T
 * @extends ArrayObject<int, T>
 */
class EntityCollection extends ArrayObject
{
    /**
     * @param array<int, T> $entities
     * @param class-string<T> $expectedClass
     */
    public function __construct(array $entities = [], private readonly string $expectedClass)
    {
        parent::__construct($entities, 0, ArrayIterator::class);
        $this->validateAll();
    }

    private function validate(mixed $value): void
    {
        if (!$value instanceof $this->expectedClass) {
            throw new InvalidArgumentException(
                sprintf('Ожидается класс коллекции %s, по факту %s', $this->expectedClass, get_debug_type($value))
            );
        }
    }

    private function validateAll(): void
    {
        foreach ($this as $item) {
            $this->validate($item);
        }
    }
}
