<?php

declare(strict_types=1);

namespace App\Services;

use App\Handlers\ResponseHandler;

class RedisPageService
{
    public function render(): void
    {
        session_start();

        $_SESSION['test_counter'] = ($_SESSION['test_counter'] ?? 0) + 1;

        $hostname = gethostname();
        $counter = $_SESSION['test_counter'];

        $redisData = (new RedisClusterService())->getData();

        $msg = "<pre>";
        $msg .= "Работаем на контейнере: $hostname\n";
        $msg .= "Сессия работает. Счетчик: $counter\n\n";

        if (isset($redisData['error'])) {
            $msg .= $redisData['error'] . "\n";
        } else {
            if (!empty($redisData['keys'])) {
                foreach ($redisData['keys'] as $data) {
                    $msg .= "Key: {$data['key']} | Value: {$data['value']}\n";
                }
            }
            if (!empty($redisData['nodes'])) {
                foreach ($redisData['nodes'] as $node) {
                    $msg .= "$node\n";
                }
            }
        }

        $msg .= "</pre>";

        (new ResponseHandler())->send(200, $msg);
    }
}
