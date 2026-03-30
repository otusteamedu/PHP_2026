<?php

declare(strict_types=1);

namespace App\Facades;

class Views
{
    public static function view(string $path)
    {

        $fullPath = BASE_PATH . '/Views/' . $path . '.html';

        if (!file_exists($fullPath)) {
            http_response_code(500);
            echo "View not found: {$fullPath}";
            return;
        }

        require $fullPath;
    }
}
