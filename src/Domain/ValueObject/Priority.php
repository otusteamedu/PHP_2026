<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Priority
{
    private function __construct(
        public int $value,
    ) {
    }

    public static function create(int $value): self
    {
        Assert::integer($value);

        return new self($value);
    }
}
