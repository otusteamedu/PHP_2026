<?php

declare(strict_types=1);

namespace App\Tests\Support;

/**
 * Хранилище состояния для подмен глобальных PHP-функций (DNS, HTTP-заголовки, php://input).
 */
final class FunctionOverrides
{
    /** @var array<string,bool> */
    public static array $dns = [];
    public static bool $dnsDefault = true;

    public static ?int $statusCode = null;
    /** @var list<string> */
    public static array $headers = [];

    public static string|false $stdin = '';

    public static function reset(): void
    {
        self::$dns = [];
        self::$dnsDefault = true;
        self::$statusCode = null;
        self::$headers = [];
        self::$stdin = '';
    }
}

namespace App\Service;

use App\Tests\Support\FunctionOverrides;

if (!function_exists(__NAMESPACE__ . '\\checkdnsrr')) {
    function checkdnsrr(string $hostname, string $type = 'MX'): bool
    {
        return FunctionOverrides::$dns[$hostname] ?? FunctionOverrides::$dnsDefault;
    }
}

namespace App\Core\Http;

use App\Tests\Support\FunctionOverrides;

if (!function_exists(__NAMESPACE__ . '\\http_response_code')) {
    function http_response_code(int $code): void
    {
        FunctionOverrides::$statusCode = $code;
    }
}

if (!function_exists(__NAMESPACE__ . '\\header')) {
    function header(string $value): void
    {
        FunctionOverrides::$headers[] = $value;
    }
}

if (!function_exists(__NAMESPACE__ . '\\file_get_contents')) {
    function file_get_contents(string $path): string|false
    {
        if ($path === 'php://input') {
            return FunctionOverrides::$stdin;
        }
        return \file_get_contents($path);
    }
}
