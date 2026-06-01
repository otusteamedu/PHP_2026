<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use JsonException;
use PHPUnit\Framework\TestCase;

final class EmailVerifyEndpointTest extends TestCase
{
    private string $baseUrl;

    protected function setUp(): void
    {
        $this->baseUrl = getenv('FUNCTIONAL_BASE_URL') ?: 'http://localhost';
    }

    /**
     * @throws JsonException
     */
    public function testValidEmailReturns200WithValidTrue(): void
    {
        [$status, $body] = $this->post('/api/email-verify', ['email' => 'support@google.com']);

        self::assertSame(200, $status);
        self::assertSame('support@google.com', $body['email']);
        self::assertTrue($body['valid']);
    }

    /**
     * @throws JsonException
     */
    public function testEmailWithMalformedDomainReturnsInvalid(): void
    {
        [$status, $body] = $this->post('/api/email-verify', ['email' => 'user@not-a-real-tld-xyz123']);

        self::assertSame(200, $status);
        self::assertFalse($body['valid']);
    }

    /**
     * @throws JsonException
     */
    public function testBatchEndpointPartitionsResults(): void
    {
        [$status, $body] = $this->post('/api/email-verify', [
            'emails' => ['support@google.com', 'bogus-not-an-email'],
        ]);

        self::assertSame(200, $status);
        self::assertContains('support@google.com', $body['valid']);
        self::assertContains('bogus-not-an-email', $body['invalid']);
    }

    /**
     * @throws JsonException
     */
    public function testUnknownRouteReturns404(): void
    {
        [$status, $body] = $this->post('/api/does-not-exist', []);

        self::assertSame(404, $status);
        self::assertSame('Route not found', $body['error']);
    }

    /**
     * @throws JsonException
     */
    public function testInvalidJsonReturns400(): void
    {
        [$status, $body] = $this->rawPost('/api/email-verify', '{broken json');

        self::assertSame(400, $status);
        self::assertSame('Invalid JSON', $body['error']);
    }

    /**
     * @throws JsonException
     */
    public function testGetMethodReturns404ViaRouter(): void
    {
        $ch = curl_init($this->baseUrl . '/api/email-verify');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
        ]);
        $raw = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        self::assertSame(404, $status);
        $payload = json_decode((string)$raw, true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('Route not found', $payload['error']);
    }

    /**
     * @return array{int, array<string,mixed>}
     * @throws JsonException
     */
    private function post(string $path, array $payload): array
    {
        return $this->rawPost($path, json_encode($payload, JSON_THROW_ON_ERROR));
    }

    /** @return array{int, array<string,mixed>}
     * @throws JsonException
     */
    private function rawPost(string $path, string $body): array
    {
        $ch = curl_init($this->baseUrl . $path);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT => 10,
        ]);

        $raw = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [$status, json_decode((string)$raw, true, 512, JSON_THROW_ON_ERROR) ?? []];
    }
}