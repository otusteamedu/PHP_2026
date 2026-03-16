<?php

declare(strict_types=1);

namespace App\app\Http\Response;

class HttpResponse
{
    public static function create(array $data): false|string
    {
        http_response_code($data['status']);

        if ($data['valid']) {
            return json_encode([
                'status' => $data['status'],
                'info' => $data['message'],
                'session_data' => $message ?? $_SERVER['HOSTNAME'],
            ]);
        } else {
            return json_encode([
                'status' => $data['status'],
                'error' => $data['error'],
                'session_data' => $message ?? $_SERVER['HOSTNAME'],
            ]);
        }
    }
}
