<?php

namespace App\Domain\Task\ValueObjects;

final class TaskTitle
{
    private function __construct(private readonly string $value) {}

    public static function fromString(string $raw): self
    {
        $value = trim($raw);
        if ($value === '') {
            throw new \InvalidArgumentException('empty');
        }
        if (strlen($value) > 255) {
            throw new \InvalidArgumentException('too_long');
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
