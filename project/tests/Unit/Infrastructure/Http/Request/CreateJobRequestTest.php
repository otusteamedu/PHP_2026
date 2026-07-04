<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Http\Request;

use App\Infrastructure\Http\Exception\ValidationException;
use App\Infrastructure\Http\Request\CreateJobRequest;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;

class CreateJobRequestTest extends TestCase
{
    public function testFromRequestWithValidName(): void
    {
        $request = $this->createRequest(['name' => '  Иван  ']);

        $createJobRequest = CreateJobRequest::fromRequest($request);

        self::assertSame('Иван', $createJobRequest->name);
    }

    public function testFromRequestWithNonArrayBody(): void
    {
        $request = $this->createRequest(null);

        self::expectException(ValidationException::class);

        CreateJobRequest::fromRequest($request);
    }

    #[TestWith([[]])]
    #[TestWith([['name' => null]])]
    #[TestWith([['name' => '']])]
    #[TestWith([['name' => '   ']])]
    #[TestWith([['name' => 123]])]
    public function testFromRequestWithInvalidName(mixed $body): void
    {
        $request = $this->createRequest($body);

        self::expectException(ValidationException::class);

        CreateJobRequest::fromRequest($request);
    }

    private function createRequest(mixed $body): ServerRequestInterface
    {
        return new ServerRequestFactory()
            ->createServerRequest('POST', '/job')
            ->withParsedBody($body);
    }
}
