<?php

declare(strict_types=1);

namespace Test\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Param;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

#[CoversClass(Param::class)]
final class ParamTest extends TestCase
{
    public function testSuccess(): void
    {
        $param = Param::create($name = 'test_param', $value = 100);

        $this->assertSame($name, $param->name);
        $this->assertSame($value, $param->value);
    }

    public function testEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Param::create('', 100);
    }
}
