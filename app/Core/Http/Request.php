<?php

declare(strict_types=1);

namespace App\Core\Http;

use JsonException;

class Request
{
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getContent(): string
    {
        return file_get_contents('php://input') ?: '';
    }

    public function json(): array
    {
        try {
            $data = json_decode($this->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new InvalidRequestException('Invalid JSON');
        }

        if (!is_array($data)) {
            throw new InvalidRequestException('JSON body must be an object');
        }

        return $data;
    }

    public function getPath(): string
    {
        return (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}
