<?php

namespace App\Domain\Task\ValueObjects;

final class TaskDescription
{
    private function __construct(private readonly string $value) {}

    public static function fromNullable(?string $raw): ?self
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $value = trim($raw);
        if (strlen($value) > 10000) {
            throw new \InvalidArgumentException('too_long');
        }

        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
