<?php

declare(strict_types=1);

namespace App\Infrastructure\Health;

use PDO;
use Throwable;

final class DatabaseHealthChecker
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function check(): bool
    {
        try {
            $this->pdo->query('SELECT 1');

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}