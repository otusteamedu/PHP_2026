<?php

declare(strict_types=1);

namespace App\Core\Http;

final readonly class HtmlResponse implements Response
{
    public function __construct(
        private string $html,
        private int $statusCode = 200,
    ) {
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: text/html; charset=utf-8');

        echo $this->html;
    }
}
