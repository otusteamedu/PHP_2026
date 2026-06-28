<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure;

use App\Domain\ValidationMessages;
use App\Infrastructure\Validation\MxValidation;
use PHPUnit\Framework\TestCase;

final class MxValidationTest extends TestCase
{
    public function test_check_returns_unknown_error_when_email_has_no_domain(): void
    {
        $rule = new MxValidation();

        $result = $rule->check('not-an-email');

        $this->assertFalse($result->isValid);
        $this->assertSame(
            ValidationMessages::UNKNOWN_ERROR->value,
            $result->message
        );
    }

    public function test_check_returns_invalid_domain_for_bad_host(): void
    {
        $rule = new MxValidation();

        $result = $rule->check('user@bad_domain');

        $this->assertFalse($result->isValid);
        $this->assertSame(
            ValidationMessages::INVALID_DOMAIN->value,
            $result->message
        );
    }

    public function test_check_returns_valid_for_existing_domain_with_mx(): void
    {
        $rule = new MxValidation();

        $result = $rule->check('user@gmail.com');

        $this->assertTrue($result->isValid);
        $this->assertSame('', $result->message);
    }

    public function test_check_returns_invalid_for_non_existing_domain(): void
    {
        $rule = new MxValidation();

        $result = $rule->check(
            'user@domain-that-definitely-does-not-exist-123456789.com'
        );

        $this->assertFalse($result->isValid);
        $this->assertSame(
            ValidationMessages::DNS_RECORD_NOT_FOUND->value,
            $result->message
        );
    }
}