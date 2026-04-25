<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Param
{
    private function __construct(
        public string $name,
        public int $value,
    ) {
    }

    public static function create(string $name, int $value): self
    {
        Assert::stringNotEmpty($name);
        Assert::integer($value);

        return new self($name, $value);
    }
}
