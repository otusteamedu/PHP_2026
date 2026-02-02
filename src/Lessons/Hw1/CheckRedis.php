<?php

declare(strict_types=1);

namespace App\Lessons\Hw1;

use Redis;
use RedisException;

class CheckRedis
{
    public static function execute(): string
    {
        try {
            $redis = new Redis();
            $redis->connect('redis');

            return 'Подключение к Redis установлено';
        } catch (RedisException $e) {
            return 'Redis: ' . $e->getMessage();
        }
    }
}
