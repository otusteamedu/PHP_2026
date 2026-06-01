<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Core\App;
use App\Tests\Support\FunctionOverrides;
use JsonException;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
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

    /**
     * @throws JsonException
     */
    public function testEndToEndSingleEmailValidationSucceeds(): void
    {
        $this->givenRequest('POST', '/api/email-verify', '{"email":"user@example.com"}');
        FunctionOverrides::$dns = ['example.com' => true];

        $body = $this->runApp();

        self::assertSame(200, FunctionOverrides::$statusCode);
        self::assertSame(
            ['email' => 'user@example.com', 'valid' => true],
            json_decode($body, true, flags: JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws JsonException
     */
    public function testEndToEndSingleEmailValidationFailsDns(): void
    {
        $this->givenRequest('POST', '/api/email-verify', '{"email":"user@unreachable.tld"}');
        FunctionOverrides::$dns = ['unreachable.tld' => false];

        $body = $this->runApp();

        self::assertSame(200, FunctionOverrides::$statusCode);
        self::assertSame(
            ['email' => 'user@unreachable.tld', 'valid' => false],
            json_decode($body, true, flags: JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws JsonException
     */
    public function testEndToEndBatchValidationPartitionsResults(): void
    {
        $this->givenRequest(
            'POST',
            '/api/email-verify',
            '{"emails":["a@good.com","wrong","b@bad.com"]}'
        );
        FunctionOverrides::$dns = ['good.com' => true, 'bad.com' => false];

        $body = $this->runApp();
        $payload = json_decode($body, true, flags: JSON_THROW_ON_ERROR);

        self::assertSame(200, FunctionOverrides::$statusCode);
        self::assertSame(['a@good.com'], $payload['valid']);
        self::assertSame(['wrong', 'b@bad.com'], $payload['invalid']);
    }

    /**
     * @throws JsonException
     */
    public function testUnknownRouteReturns404(): void
    {
        $this->givenRequest('POST', '/api/nope', '{}');

        $body = $this->runApp();

        self::assertSame(404, FunctionOverrides::$statusCode);
        self::assertSame(
            ['success' => false, 'error' => 'Route not found'],
            json_decode($body, true, flags: JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws JsonException
     */
    public function testGetMethodOnVerifyEndpointReturns404(): void
    {
        // GET /api/email-verify is not registered → Router::dispatch throws → 404.
        $this->givenRequest('GET', '/api/email-verify', '');

        $body = $this->runApp();

        self::assertSame(404, FunctionOverrides::$statusCode);
        self::assertSame('Route not found', json_decode($body, true, 512, JSON_THROW_ON_ERROR)['error']);
    }

    /**
     * @throws JsonException
     */
    public function testInvalidJsonBodyReturns400FromController(): void
    {
        $this->givenRequest('POST', '/api/email-verify', '{not-json');

        $body = $this->runApp();

        self::assertSame(400, FunctionOverrides::$statusCode);
        self::assertSame('Invalid JSON', json_decode($body, true, 512, JSON_THROW_ON_ERROR)['error']);
    }

    /**
     * @throws JsonException
     */
    public function testEmptyBodyReturns400FromController(): void
    {
        $this->givenRequest('POST', '/api/email-verify', '');

        $body = $this->runApp();

        self::assertSame(400, FunctionOverrides::$statusCode);
        self::assertSame(
            'Invalid JSON',
            json_decode($body, true, 512, JSON_THROW_ON_ERROR)['error'],
        );
    }

    private function givenRequest(string $method, string $path, string $body): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $path;
        FunctionOverrides::$stdin = $body;
    }

    /**
     * @throws JsonException
     */
    private function runApp(): string
    {
        ob_start();
        (new App())->run();
        return (string)ob_get_clean();
    }
}
