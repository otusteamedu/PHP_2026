<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Http;

use App\Core\Http\Request;
use App\Tests\Support\FunctionOverrides;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    /** @var array<string,mixed> */
    private array $serverBackup;

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
        FunctionOverrides::reset();
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
    }

    public function testGetMethodReturnsServerValue(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        self::assertSame('POST', (new Request())->getMethod());
    }

    public function testGetMethodDefaultsToGet(): void
    {
        unset($_SERVER['REQUEST_METHOD']);

        self::assertSame('GET', (new Request())->getMethod());
    }

    public function testGetPathParsesUri(): void
    {
        $_SERVER['REQUEST_URI'] = '/api/email-verify?foo=bar';

        self::assertSame('/api/email-verify', (new Request())->getPath());
    }

    public function testGetPathReturnsPlainPathWhenNoQuery(): void
    {
        $_SERVER['REQUEST_URI'] = '/health';

        self::assertSame('/health', (new Request())->getPath());
    }

    public function testGetContentReturnsInputStream(): void
    {
        FunctionOverrides::$stdin = '{"email":"x@y.z"}';

        self::assertSame('{"email":"x@y.z"}', (new Request())->getContent());
    }

    public function testGetContentReturnsEmptyStringWhenStreamFails(): void
    {
        FunctionOverrides::$stdin = false;

        self::assertSame('', (new Request())->getContent());
    }
}
