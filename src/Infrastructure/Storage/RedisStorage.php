<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use Redis;

final readonly class RedisStorage implements Storage
{
    public function __construct(
        private Redis $redis,
    ) {
    }

    public function set(string $key, int $score, string $value): void
    {
        $this->redis->zAdd($key, $score, $value);
    }

    public function get(string $key): array
    {
        return $this->redis->zRevRange($key, 0, 0, true);
    }

    public function deleteAll(): void
    {
        $this->redis->flushDB();
    }
}
