<?php

declare(strict_types=1);

namespace App\Infrastructure;

abstract class Storage
{
    abstract public function get(string $key, string $value): string;
    abstract public function set(string $key, string $value): void;
}
