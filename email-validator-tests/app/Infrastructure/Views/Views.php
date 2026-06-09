<?php

declare(strict_types=1);

namespace App\Infrastructure\Views;

class Views
{
    public static function view(string $path)
    {

        $fullPath = BASE_PATH . '/resources/Views/' . $path . '.html';

        if (!file_exists($fullPath)) {
            http_response_code(500);
            echo "View not found: {$fullPath}";
            return;
        }

        require $fullPath;
    }
}
