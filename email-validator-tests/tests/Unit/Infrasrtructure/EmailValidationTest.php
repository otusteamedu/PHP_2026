<?php

declare (strict_types = 1);

namespace Tests\Unit\Infrastructure;

use App\Domain\ValidationMessages;
use App\Infrastructure\Validation\EmailValidation;
use PHPUnit\Framework\TestCase;

final class EmailValidationTest extends TestCase
{
    public function test_check_returns_valid_for_correct_email(): void
    {
        $rule = new EmailValidation();

        $result = $rule->check('user@example.com');

        $this->assertTrue($result->isValid);
        $this->assertSame('', $result->message);
    }

    public function test_check_returns_invalid_for_bad_email_format(): void
    {
        $rule = new EmailValidation();

        $result = $rule->check('bad-email');

        $this->assertFalse($result->isValid);
        $this->assertSame(ValidationMessages::INVALID_FORMAT->value, $result->message);
    }

    public function test_check_returns_invalid_for_email_longer_than_254_characters(): void
    {
        $rule = new EmailValidation();

        $email = str_repeat('a', 245) . '@example.com';

        $this->assertGreaterThan(254, strlen($email));

        $result = $rule->check($email);

        $this->assertFalse($result->isValid);
        $this->assertSame(
            ValidationMessages::INVALID_FORMAT->value,
            $result->message
        );
    }
}
