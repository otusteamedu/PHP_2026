<?php

declare(strict_types=1);

namespace App\Core\Http;

use RuntimeException;

final class View
{
    /**
     * @param array<string, scalar> $vars
     */
    public static function render(string $template, array $vars = []): string
    {
        $file = __DIR__ . '/../../../templates/' . $template . '.php';

        if (!is_file($file)) {
            throw new RuntimeException('Template not found: ' . $template);
        }

        extract($vars);

        ob_start();
        require $file;

        return (string) ob_get_clean();
    }
}
