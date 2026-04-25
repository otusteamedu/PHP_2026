<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use Memcached;

final readonly class MemcachedStorage implements Storage
{
    public function __construct(
        private Memcached $memcached,
    ) {
    }

    public function set(string $key, int $score, string $value): void
    {
        $data = $this->memcached->get($key) ?: [];

        $data[$value] = $score;
        arsort($data);

        $this->memcached->set($key, $data);
    }

    public function get(string $key): array
    {
        $data = $this->memcached->get($key);

        return is_array($data) ? array_slice($data, 0, 1, true) : [];
    }

    public function deleteAll(): void
    {
        $this->memcached->flush();
    }
}
