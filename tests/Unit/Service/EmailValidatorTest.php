<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\EmailValidator;
use App\Tests\Support\FunctionOverrides;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EmailValidatorTest extends TestCase
{
    protected function setUp(): void
    {
        FunctionOverrides::reset();
    }

    public static function invalidFormatProvider(): array
    {
        return [
            'empty string' => [''],
            'no @ sign' => ['no-at-sign'],
            'no local part' => ['@example.com'],
            'no domain' => ['user@'],
            'two @ signs' => ['a@b@c.com'],
            'space inside' => ['user name@example.com'],
            'plain word' => ['hello'],
        ];
    }

    #[DataProvider('invalidFormatProvider')]
    public function testReturnsFalseForInvalidFormat(string $email): void
    {
        FunctionOverrides::$dnsDefault = true;

        self::assertFalse((new EmailValidator())->isValid($email));
    }

    public function testReturnsTrueWhenFormatValidAndDnsResolves(): void
    {
        FunctionOverrides::$dns = ['example.com' => true];

        self::assertTrue((new EmailValidator())->isValid('user@example.com'));
    }

    public function testReturnsFalseWhenFormatValidButDnsFails(): void
    {
        FunctionOverrides::$dns = ['no-such-domain.tld' => false];

        self::assertFalse((new EmailValidator())->isValid('user@no-such-domain.tld'));
    }

    public function testFilterValidKeepsOnlyValidEmails(): void
    {
        FunctionOverrides::$dns = [
            'good.com' => true,
            'bad.com'  => false,
        ];

        $input = ['a@good.com', 'broken', 'b@bad.com', 'c@good.com'];
        $result = (new EmailValidator())->filterValid($input);

        self::assertSame(['a@good.com', 'c@good.com'], array_values($result));
    }

    public function testFilterValidWithEmptyArrayReturnsEmptyArray(): void
    {
        self::assertSame([], (new EmailValidator())->filterValid([]));
    }

    public function testFilterValidPreservesArrayKeys(): void
    {
        FunctionOverrides::$dns = ['ok.com' => true];

        $result = (new EmailValidator())->filterValid([
            0 => 'bad',
            1 => 'user@ok.com',
            2 => 'also-bad',
        ]);

        self::assertSame([1 => 'user@ok.com'], $result);
    }
}