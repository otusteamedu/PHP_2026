<?php

declare(strict_types=1);

namespace Test;

use App\EmailValidator;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    #[TestWith(['', false])]
    #[TestWith(['expecto patronum', false])]
    #[TestWith(['invalid@mai@l.ru', false])]
    #[TestWith(['test@nonexistingsubdomain.example.com', false])]
    #[TestWith(['correct@gmail.com', true])]
    public function testValidate(string $email, bool $expected): void
    {
        $validator = new EmailValidator();
        $this->assertEquals($expected, $validator->validate($email));
    }
}
