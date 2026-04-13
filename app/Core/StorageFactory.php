<?php

declare(strict_types=1);

namespace App\Core;

use App\Storage\MongoStorage;
use App\Storage\RedisStorage;
use App\Storage\StorageInterface;
use MongoDB\Driver\Manager;
use Redis;

final class StorageFactory
{
    public static function create(): StorageInterface
    {
        $driver = getenv('STORAGE_DRIVER') ?: 'redis';

        if ($driver === 'mongodb') {
            $host = getenv('MONGO_HOST') ?: 'mongodb';
            $port = getenv('MONGO_PORT') ?: '27017';
            $db   = getenv('MONGO_DB') ?: 'events';
            $user = getenv('MONGO_USER');
            $pass = getenv('MONGO_PASSWORD');

            $dsn = ($user && $pass) ? "mongodb://$user:$pass@$host:$port" : "mongodb://$host:$port";

            return new MongoStorage(new Manager($dsn), $db);
        }

        $redis = new Redis();
        $redis->connect(getenv('REDIS_HOST') ?: 'redis', (int) (getenv('REDIS_PORT') ?: 6379));

        if ($pass = getenv('REDIS_PASSWORD')) {
            $redis->auth($pass);
        }

        return new RedisStorage($redis);
    }
}
