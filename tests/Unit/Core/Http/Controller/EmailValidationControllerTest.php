<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Http\Controller;

use App\Core\Http\Controller\EmailValidationController;
use App\Core\Http\JsonResponse;
use App\Core\Http\Request;
use App\Service\EmailValidator;
use App\Tests\Support\FunctionOverrides;
use PHPUnit\Framework\TestCase;

final class EmailValidationControllerTest extends TestCase
{
    protected function setUp(): void
    {
        FunctionOverrides::reset();
    }

    public function testReturns405WhenMethodIsNotPost(): void
    {
        $controller = new EmailValidationController(new EmailValidator());

        $response = $controller->emailVerify($this->makeRequest('GET', ''));

        $payload = $this->payloadOf($response);
        self::assertSame(405, $this->statusOf($response));
        self::assertFalse($payload['success']);
        self::assertSame('Only POST allowed', $payload['error']);
    }

    public function testReturnsValidationResultForValidSingleEmail(): void
    {
        FunctionOverrides::$dns = ['example.com' => true];

        $response = (new EmailValidationController(new EmailValidator()))
            ->emailVerify($this->makeRequest('POST', '{"email":"user@example.com"}'));

        self::assertSame(200, $this->statusOf($response));
        self::assertSame(
            ['email' => 'user@example.com', 'valid' => true],
            $this->payloadOf($response)
        );
    }

    public function testReturnsValidationResultForInvalidSingleEmail(): void
    {
        FunctionOverrides::$dnsDefault = false;

        $response = (new EmailValidationController(new EmailValidator()))
            ->emailVerify($this->makeRequest('POST', '{"email":"not-an-email"}'));

        self::assertSame(200, $this->statusOf($response));
        self::assertSame(
            ['email' => 'not-an-email', 'valid' => false],
            $this->payloadOf($response)
        );
    }

    public function testCoercesNonStringEmailToString(): void
    {
        FunctionOverrides::$dnsDefault = false;

        $response = (new EmailValidationController(new EmailValidator()))
            ->emailVerify($this->makeRequest('POST', '{"email":42}'));

        self::assertSame(
            ['email' => '42', 'valid' => false],
            $this->payloadOf($response)
        );
    }

    public function testPartitionsEmailsIntoValidAndInvalid(): void
    {
        FunctionOverrides::$dns = ['good.com' => true, 'bad.com' => false];

        $response = (new EmailValidationController(new EmailValidator()))
            ->emailVerify($this->makeRequest(
                'POST',
                '{"emails":["a@good.com","broken","b@bad.com","c@good.com"]}'
            ));

        $payload = $this->payloadOf($response);
        self::assertSame(['a@good.com', 'c@good.com'], array_values($payload['valid']));
        self::assertSame(['broken', 'b@bad.com'], $payload['invalid']);
    }

    public function testReturns400WhenNeitherEmailNorEmailsProvided(): void
    {
        $response = (new EmailValidationController(new EmailValidator()))
            ->emailVerify($this->makeRequest('POST', '{"foo":"bar"}'));

        self::assertSame(400, $this->statusOf($response));
        self::assertSame('Missing "email" or "emails" field', $this->payloadOf($response)['error']);
    }

    public function testReturns400WhenEmailsFieldIsNotArray(): void
    {
        $response = (new EmailValidationController(new EmailValidator()))
            ->emailVerify($this->makeRequest('POST', '{"emails":"oops"}'));

        self::assertSame(400, $this->statusOf($response));
        self::assertSame('Missing "email" or "emails" field', $this->payloadOf($response)['error']);
    }

    public function testReturns400OnInvalidJson(): void
    {
        $response = (new EmailValidationController(new EmailValidator()))
            ->emailVerify($this->makeRequest('POST', '{not json'));

        self::assertSame(400, $this->statusOf($response));
        self::assertSame('Invalid JSON', $this->payloadOf($response)['error']);
    }

    public function testEmailFieldTakesPrecedenceOverEmailsField(): void
    {
        FunctionOverrides::$dns = ['x.com' => true];

        $response = (new EmailValidationController(new EmailValidator()))
            ->emailVerify($this->makeRequest(
                'POST',
                '{"email":"one@x.com","emails":["other@y.com"]}'
            ));

        self::assertSame(['email' => 'one@x.com', 'valid' => true], $this->payloadOf($response));
    }

    private function makeRequest(string $method, string $body): Request
    {
        return new class($method, $body) extends Request {
            public function __construct(
                private readonly string $method,
                private readonly string $body,
            ) {}

            public function getMethod(): string
            {
                return $this->method;
            }

            public function getContent(): string
            {
                return $this->body;
            }
        };
    }

    private function statusOf(JsonResponse $response): int
    {
        $ref = new \ReflectionClass($response);
        return $ref->getProperty('statusCode')->getValue($response);
    }

    private function payloadOf(JsonResponse $response): array
    {
        $ref = new \ReflectionClass($response);
        return $ref->getProperty('data')->getValue($response);
    }
}
