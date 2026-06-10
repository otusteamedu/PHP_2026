<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Http\JsonResponse;

require __DIR__ . '/../bootstrap.php';

try {
    (new App())->run();
} catch (Throwable $e) {
    (new JsonResponse(['error' => $e->getMessage()], 500))->send();
}
