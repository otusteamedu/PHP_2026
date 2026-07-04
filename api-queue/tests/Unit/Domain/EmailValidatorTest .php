<?php

declare (strict_types = 1);

namespace Tests\Unit\Domain;

use App\Domain\EmailValidationRuleInterface;
use App\Domain\EmailValidator;
use App\Domain\ValidationResult;
use PHPUnit\Framework\TestCase;

final class EmailValidatorTest extends TestCase
{
    public function test_validate_stops_on_first_invalid_rule(): void
    {
        $firstRule = $this->createMock(EmailValidationRuleInterface::class);
        $firstRule->expects($this->once())
            ->method('check')
            ->with('test@example.com')
            ->willReturn(new ValidationResult(false, 'first rule failed'));

        $secondRule = $this->createMock(EmailValidationRuleInterface::class);
        $secondRule->expects($this->never())->method('check');

        $validator = new EmailValidator([$firstRule, $secondRule]);

        $result = $validator->validate('test@example.com');

        $this->assertFalse($result->isValid);
        $this->assertSame('first rule failed', $result->message);
    }

    public function test_validate_returns_valid_when_all_rules_pass(): void
    {
        $firstRule = $this->createMock(EmailValidationRuleInterface::class);
        $firstRule->method('check')->willReturn(new ValidationResult(true));

        $secondRule = $this->createMock(EmailValidationRuleInterface::class);
        $secondRule->method('check')->willReturn(new ValidationResult(true));

        $validator = new EmailValidator([$firstRule, $secondRule]);

        $result = $validator->validate('test@example.com');

        $this->assertTrue($result->isValid);
        $this->assertSame('', $result->message);
    }

    public function test_validate_returns_valid_when_no_rules_configured(): void
    {
        $validator = new EmailValidator([]);

        $result = $validator->validate('test@example.com');

        $this->assertTrue($result->isValid);
        $this->assertSame('', $result->message);
    }

    public function test_validate_processes_multiple_rules_until_failure(): void
    {
        $firstRule = $this->createMock(EmailValidationRuleInterface::class);
        $firstRule->expects($this->once())
            ->method('check')
            ->willReturn(new ValidationResult(true));

        $secondRule = $this->createMock(EmailValidationRuleInterface::class);
        $secondRule->expects($this->once())
            ->method('check')
            ->willReturn(new ValidationResult(false, 'second failed'));

        $validator = new EmailValidator([$firstRule, $secondRule]);

        $result = $validator->validate('test@example.com');

        $this->assertFalse($result->isValid);
        $this->assertSame('second failed', $result->message);
    }
}
