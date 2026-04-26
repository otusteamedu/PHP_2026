<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Event
{
    private function __construct(
        public string $value,
    ) {
    }

    public static function create(string $value): self
    {
        Assert::stringNotEmpty($value);

        return new self($value);
    }
}
