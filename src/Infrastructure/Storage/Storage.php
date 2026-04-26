<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

interface Storage
{
    public function set(string $key, int $score, string $value): void;

    public function get(string $key): array;

    public function deleteAll(): void;
}
