<?php

declare (strict_types = 1);

namespace Tests\Unit\Application;

use App\Application\ValidateEmailsUseCases;
use App\Domain\EmailValidator;
use App\Domain\ValidationResult;
use PHPUnit\Framework\TestCase;

final class ValidateEmailsUseCaseTest extends TestCase
{
    public function test_execute_returns_mapped_results(): void
    {
        $validator = $this->createMock(EmailValidator::class);

        $validator->expects($this->exactly(2))
            ->method('validate')
            ->willReturnOnConsecutiveCalls(
                new ValidationResult(true),
                new ValidationResult(false, 'invalid format')
            );

        $useCase = new ValidateEmailsUseCases($validator);

        $result = $useCase->execute(['first@example.com', 'second']);

        $this->assertSame([
            [
                'email'   => 'first@example.com',
                'status'  => 'valid',
                'message' => '',
            ],
            [
                'email'   => 'second',
                'status'  => 'invalid',
                'message' => 'invalid format',
            ],
        ], $result);
    }

    public function test_execute_returns_empty_array_when_no_emails_provided(): void
    {
        $validator = $this->createMock(EmailValidator::class);

        $validator->expects($this->never())
            ->method('validate');

        $useCase = new ValidateEmailsUseCases($validator);

        $result = $useCase->execute([]);

        $this->assertSame([], $result);
    }
}
