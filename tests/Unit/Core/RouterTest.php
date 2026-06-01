<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core;

use App\Core\Http\Request;
use App\Core\Router;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class RouterTest extends TestCase
{
    public function testDispatchReturnsRegisteredHandler(): void
    {
        $router  = new Router();
        $handler = function (): string {
            return 'hit';
        };
        $router->add('POST', '/api/email-verify', $handler);

        $request = $this->makeRequest('POST', '/api/email-verify');

        $result = $router->dispatch($request);

        self::assertSame($handler, $result);
        self::assertSame('hit', $result());
    }

    public function testDispatchThrowsWhenMethodNotRegistered(): void
    {
        $router = new Router();
        $router->add('POST', '/api/email-verify', function (): void {
        });

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Route not found');

        $router->dispatch($this->makeRequest('GET', '/api/email-verify'));
    }

    public function testDispatchThrowsWhenPathNotRegistered(): void
    {
        $router = new Router();
        $router->add('POST', '/api/email-verify', function (): void {
        });

        $this->expectException(RuntimeException::class);

        $router->dispatch($this->makeRequest('POST', '/api/unknown'));
    }

    public function testDispatchThrowsForEmptyRouter(): void
    {
        $this->expectException(RuntimeException::class);

        (new Router())->dispatch($this->makeRequest('GET', '/'));
    }

    public function testLaterAddOverridesEarlierRouteOnSameKey(): void
    {
        $router = new Router();
        $router->add('GET', '/x', function (): string {
            return 'first';
        });
        $router->add('GET', '/x', function (): string {
            return 'second';
        });

        $handler = $router->dispatch($this->makeRequest('GET', '/x'));

        self::assertSame('second', $handler());
    }

    private function makeRequest(string $method, string $path): Request
    {
        return new class($method, $path) extends Request {
            public function __construct(
                private readonly string $method,
                private readonly string $path,
            ) {}

            public function getMethod(): string
            {
                return $this->method;
            }

            public function getPath(): string
            {
                return $this->path;
            }
        };
    }
}
