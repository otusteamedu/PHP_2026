<?php

declare (strict_types = 1);

namespace App\Infrastructure;

final class RedisFactory
{
    public static function create(): \Redis
    {
        $redis = new \Redis();
        $redis->connect('redis', 6379);

        return $redis;
    }
}