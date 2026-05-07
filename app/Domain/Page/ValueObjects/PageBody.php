<?php

namespace App\Domain\Page\ValueObjects;

final class PageBody
{
    private function __construct(private readonly ?string $value) {}

    public static function fromNullable(?string $raw): ?self
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        return new self($raw);
    }

    public function toPlain(): ?string
    {
        return $this->value;
    }
}
