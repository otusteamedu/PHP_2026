<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use Redis;

final class RedisStorage extends Storage
{
    public function __construct(
        private readonly Redis $redis,
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
}
