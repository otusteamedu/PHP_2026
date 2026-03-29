<?php

declare(strict_types=1);

namespace App\Handlers;

class RequestHandler
{
    public function getString(): string
    {
        $value = $_POST['string'] ?? null;

        if (!is_string($value)) {
            throw new \InvalidArgumentException('В запросе параметр string должен быть строкой');
        }

        return $value;
    }
}
