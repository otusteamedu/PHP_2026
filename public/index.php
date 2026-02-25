<?php

declare(strict_types=1);

use App\Core\Http\JsonResponse;

require __DIR__ . '/../vendor/autoload.php';

try {
    new \App\Core\App()->run();
} catch (Throwable $e) {
    return new JsonResponse(['error' => $e->getMessage()], 500);
}
