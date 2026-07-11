<?php

declare (strict_types = 1);

namespace App\HTTP\Controllers;
use App\Infrastructure\Health\HealthService;

class HealthCheckController
{
   public function __construct(
        private HealthService $health
    ) {}

    public function check(): void
    {
        $result = $this->health->check();

        http_response_code(
            $result['status'] === 'ok'
                ? 200
                : 503
        );

        header('Content-Type: application/json');

        echo json_encode($result);
    }
}
