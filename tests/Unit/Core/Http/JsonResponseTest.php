<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Http;

use App\Core\Http\JsonResponse;
use App\Tests\Support\FunctionOverrides;
use JsonException;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Framework\TestCase;

final class JsonResponseTest extends TestCase
{
    protected function setUp(): void
    {
        FunctionOverrides::reset();
    }

    /**
     * @throws JsonException
     */
    public function testSendUsesDefaultStatus200(): void
    {
        $response = new JsonResponse(['ok' => true]);

        $body = $this->send($response);

        self::assertSame(200, FunctionOverrides::$statusCode);
        self::assertSame(['Content-Type: application/json'], FunctionOverrides::$headers);
        self::assertSame('{"ok":true}', $body);
    }

    /**
     * @throws JsonException
     */
    public function testSendHonoursCustomStatusCode(): void
    {
        $response = new JsonResponse(['error' => 'oops'], 500);

        $this->send($response);

        self::assertSame(500, FunctionOverrides::$statusCode);
    }

    /**
     * @throws JsonException
     */
    public function testSendEncodesUnicodeUnescaped(): void
    {
        $response = new JsonResponse(['name' => 'Алексей']);

        $body = $this->send($response);

        self::assertStringContainsString('Алексей', $body);
        self::assertStringNotContainsString('\\u', $body);
    }

    public function testSendThrowsOnInvalidJson(): void
    {
        $response = new JsonResponse(['bad' => "\xB1\x31"]);

        $thrown = null;
        try {
            $this->send($response);
        } catch (JsonException $e) {
            $thrown = $e;
        }

        self::assertInstanceOf(JsonException::class, $thrown);
    }

    /**
     * @throws JsonException
     */
    public function testSendHandlesNestedStructure(): void
    {
        $payload = ['valid' => ['a@b.com'], 'invalid' => ['x']];
        $response = new JsonResponse($payload);

        $body = $this->send($response);

        self::assertSame($payload, json_decode($body, true, flags: JSON_THROW_ON_ERROR));
    }

    /**
     * @throws JsonException
     */
    private function send(JsonResponse $response): string
    {
        ob_start();
        $response->send();
        return (string)ob_get_clean();
    }
}
