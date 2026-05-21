<?php

namespace test;

use PHPUnit\Framework\TestCase;
use App\Services\EmailVerificationService;

class EmailVerificationServiceTest extends TestCase
{
    private EmailVerificationService $service;

    protected function setUp(): void
    {
        $this->service = new EmailVerificationService();
    }

    public function testCheckEmailWhenEmailValid(): void
    {
        $this->assertFalse($this->service->checkEmail('example@example.ru'));
    }

    public function testCheckEmailWhenEmailInvalid(): void
    {
        $this->assertTrue($this->service->checkEmail('example@yandex.ru'));
    }

    public function testCheckEmailListWhenEmailsValid(): void
    {
        $imails = [
            'example@example.ru',
            'example2@example.ru'
        ];

        $expected = [
            ['email' => 'example@example.ru', 'is_valid' => false],
            ['email' => 'example2@example.ru', 'is_valid' => false]
        ];

        $result = $this->service->checkEmailList($imails);

        $this->assertIsArray($result);
        $this->assertEquals($expected, $result);
    }

    public function testCheckEmailWhenListEmailsInvalid(): void
    {
        $imails = [
            'example@yandex.ru',
            'example2@yandex.ru'
        ];

        $expected = [
            ['email' => 'example@yandex.ru', 'is_valid' => true],
            ['email' => 'example2@yandex.ru', 'is_valid' => true]
        ];

        $result = $this->service->checkEmailList($imails);

        $this->assertIsArray($result);
        $this->assertEquals($expected, $result);
    }
}
