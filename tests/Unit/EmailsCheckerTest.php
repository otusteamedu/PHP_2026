<?php

declare(strict_types=1);

namespace Test\Unit;

use App\EmailsChecker;
use App\EmailValidator;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class EmailsCheckerTest extends TestCase
{
    #[TestWith([[], false])]
    #[TestWith([['correct@gmail.com', 'correct@mail.com'], true])]
    #[TestWith([['invalid@mai@l.ru', 'correct@mail.com'], false])]
    public function testValidate(array $emails, bool $expected): void
    {
        $checker = new EmailsChecker(new EmailValidator());
        $this->assertEquals($expected, $checker->check($emails));
    }
}
