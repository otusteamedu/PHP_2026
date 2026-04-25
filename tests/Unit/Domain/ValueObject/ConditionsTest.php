<?php

declare(strict_types=1);

namespace Test\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Conditions;
use App\Domain\ValueObject\Param;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

#[CoversClass(Conditions::class)]
final class ConditionsTest extends TestCase
{
    public function testSuccess(): void
    {
        $params = [
            'param1' => 10,
            'param2' => 20,
        ];

        $conditions = Conditions::create($params);

        $this->assertCount(2, $conditions->params);
        $this->assertInstanceOf(Param::class, $conditions->params[0]);
        $this->assertSame('param1', $conditions->params[0]->name);
        $this->assertSame(10, $conditions->params[0]->value);
        $this->assertSame('param2', $conditions->params[1]->name);
        $this->assertSame(20, $conditions->params[1]->value);
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Conditions::create([]);
    }
}
