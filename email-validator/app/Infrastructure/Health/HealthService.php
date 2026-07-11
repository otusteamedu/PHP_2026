<?php

declare(strict_types=1);

namespace App\Infrastructure\Health;

final class HealthService
{
    public function __construct(
        private DatabaseHealthChecker $database,
        private RabbitHealthChecker $rabbit
    ) {}

    public function check(): array
    {
        $database = $this->database->check();
        $rabbit = $this->rabbit->check();

        return [
            'status' => $database && $rabbit ? 'ok' : 'error',

            'services' => [
                'database' => $database ? 'up' : 'down',
                'rabbitmq' => $rabbit ? 'up' : 'down',
            ]
        ];
    }
}