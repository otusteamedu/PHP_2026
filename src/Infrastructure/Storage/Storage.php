<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

abstract class Storage
{
    abstract public function set(string $key, int $score, string $value): void;

    abstract public function get(string $key): array;
}
