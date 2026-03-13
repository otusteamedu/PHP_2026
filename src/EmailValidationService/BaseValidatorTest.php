<?php

declare(strict_types=1);

namespace App\EmailValidationService;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BaseValidatorTest extends TestCase
{
    #[DataProvider('emailDataProvider')]
    public function testCheckProvidedEmails(string $email, bool $expectedResults): void
    {
        $result = BaseValidator::checkEmail($email);

        self::assertSame($expectedResults, $result);
    }

    public static function emailDataProvider(): array
    {
         return [
             ['test@gmail.com', true],
             ['@gmail.com', false],
             ['test@.com', false],
             ['test@gmail', false],
             ['test@noexisting-domain.com', false],
             ['', false],
             ['test with space@gmail.com', false],
             ['ВасилийЧапаев@gmail.com', false],
         ];
    }
}
