<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use RedisCluster;

class RedisClusterService
{
    protected array $seeds;
    protected string $password;
    protected ?RedisCluster $instance = null;

    public function __construct(array $seeds = [], ?string $password = null)
    {
        $this->seeds = !empty($seeds) ? $seeds : [
            'redis-node-1:6379',
            'redis-node-2:6379',
            'redis-node-3:6379'
        ];

        $this->password = $password ?? (string)getenv('REDIS_PASSWORD');
    }

    public function getConnection(): RedisCluster
    {
        if ($this->instance === null) {
            $this->instance = new RedisCluster(
                'app-cluster',
                $this->seeds,
                1.5,
                1.5,
                true,
                $this->password
            );
        }

        return $this->instance;
    }

    public function getData(): array
    {
        $result = [];

        try {
            $redis = $this->getConnection();

            $testKeys = [
                'session_1',
                'user_data',
                'cache_key_test',
                'system_status'
            ];

            foreach ($testKeys as $key) {
                $value = "data_for_" . $key . "_" . time();
                $redis->set($key, $value);
                $savedValue = $redis->get($key);
                $result['keys'][] = ['key' => $key, 'value' => $savedValue];
            }

            foreach ($redis->_masters() as $nodeInfo) {
                $result['nodes'][] = "{$nodeInfo[0]}:{$nodeInfo[1]} ONLINE";
            }
        } catch (Exception $e) {
            $result['error'] = "Ошибка подключения к Redis: " . $e->getMessage();
        }
        return $result;
    }
}
