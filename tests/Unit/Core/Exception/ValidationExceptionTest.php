<?php

declare(strict_types=1);

namespace App\Tests\Unit\Core\Exception;

use App\Core\Exception\ValidationException;
use DomainException;
use PHPUnit\Framework\TestCase;

final class ValidationExceptionTest extends TestCase
{
    public function testIsDomainException(): void
    {
        self::assertInstanceOf(DomainException::class, new ValidationException('x'));
    }

    public function testCarriesMessage(): void
    {
        $exception = new ValidationException('bad input');

        self::assertSame('bad input', $exception->getMessage());
    }
}
