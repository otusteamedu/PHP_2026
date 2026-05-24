<?php

namespace test;

use PHPUnit\Framework\TestCase;
use App\Services\EmailVerificationService;
use InvalidArgumentException;
use TypeError;

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

     // Новые тесты

    /**
     * Тест: проверка null-значения
     */
    public function testCheckEmailWithNull(): void
    {
        $this->expectException(TypeError::class);
        $this->service->checkEmail(null);
    }

    /**
     * Тест: очень длинный но до 254 символов email
     */
    public function testCheckEmailWithVeryLongAddressButLessThan254(): void
    {
        $longEmail = str_repeat('a', 241) . '@example.com'; // Максимальная длина email — 254 символа
        $this->assertFalse($this->service->checkEmail($longEmail));
    }

    /**
     * Тест: очень длинный email
     */
    public function testCheckEmailWithVeryLongAddress(): void
    {
        $longEmail = str_repeat('a', 255) . '@example.com'; // Максимальная длина email — 254 символа
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be longer than 254 characters');
        $this->service->checkEmail($longEmail);
    }

    /**
     * Тест: email с несколькими уровнями поддоменов
     */
    public function testCheckEmailWithMultipleSubdomains(): void
    {
        $email = 'user@sub1.sub2.sub3.example.com';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email must contain only one dot after the @ symbol');
        $this->service->checkEmail($email);
    }

    /**
     * Тест: отсутствие символа @
     */
    public function testCheckEmailWithoutAtSymbol(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email must contain at least one @ symbol');
        $this->service->checkEmail('example.example.ru');
    }

    /**
     * Тест: несколько символов @
     */
    public function testCheckEmailWithMultipleAtSymbols(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email must contain only one @ symbol');
        $this->service->checkEmail('ex@ample@example.ru');
    }

    /**
     * Тест: пробелы в email
     */
    public function testCheckEmailWithSpaces(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot contain spaces');
        $this->service->checkEmail(' example@example.ru');
    }

    /**
     * Тест: смешанный список email (валидные + невалидные)
     */
    public function testCheckEmailListWithMixedEmails(): void
    {
        $emails = [
            'valid@example.com',
            'invalid@yandex.ru',
            'another@valid@domain.org'
        ];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email must contain only one @ symbol');
        $this->service->checkEmailList($emails);
    }

    /**
     * Тест: пустой массив email
     */
    public function testCheckEmailListWithEmptyArray(): void
    {
        $result = $this->service->checkEmailList([]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Тест: ппроверка на несколько точек подряд
     */
    public function testCheckEmailWithConsecutiveDots(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot contain consecutive dots');
        $this->service->checkEmail('example..@example.ru');
    }
}
