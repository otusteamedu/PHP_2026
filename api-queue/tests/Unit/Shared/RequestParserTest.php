<?php

declare (strict_types = 1);

namespace Tests\Unit\Shared;

use App\Shared\RequestParser;
use PHPUnit\Framework\TestCase;

final class RequestParserTest extends TestCase
{
    public function test_parse_trims_and_removes_duplicates(): void
    {
        $parser = new RequestParser(10);

        $request = json_encode([
            'emails' => [
                ' first@example.com ',
                'first@example.com',
                ' second@example.com ',
                'SECOND@example.com',
            ],
        ]);

        $result = $parser->parse($request);

        $this->assertSame([
            'first@example.com',
            'second@example.com',
        ], $result);
    }

    public function test_parse_throws_on_invalid_json(): void
    {
        $parser = new RequestParser(10);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(400);

        $parser->parse('{json');
    }

    public function test_parse_throws_on_empty_request_body(): void
    {
        $parser = new RequestParser(10);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(422);

        $parser->parse(json_encode([]));
    }

    public function test_parse_throws_when_no_email_addresses_left_after_filtering(): void
    {
        $parser = new RequestParser(10);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(422);

        $parser->parse(json_encode([
            'emails' => ['', '   ', 123, false],
        ]));
    }

    public function test_parse_throws_when_limit_is_exceeded(): void
    {
        $parser = new RequestParser(1);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(422);

        $parser->parse(json_encode([
            'emails' => ['first@example.com', 'second@example.com'],
        ]));
    }

    public function test_parse_ignores_non_string_values(): void
    {
        $parser = new RequestParser(10);

        $result = $parser->parse(json_encode([
            'emails' => [
                'test@example.com',
                123,
                true,
                [],
                null,
            ],
        ]));

        $this->assertSame([
            'test@example.com',
        ], $result);
    }

    public function test_parse_preserves_order_of_unique_emails(): void
    {
        $parser = new RequestParser(10);

        $result = $parser->parse(json_encode([
            'emails' => [
                'first@example.com',
                'second@example.com',
                'first@example.com',
                'third@example.com',
            ],
        ]));

        $this->assertSame([
            'first@example.com',
            'second@example.com',
            'third@example.com',
        ], $result);
    }
}
