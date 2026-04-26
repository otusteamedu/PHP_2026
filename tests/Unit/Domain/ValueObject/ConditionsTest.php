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

    public function testFromKey()
    {
        $key = 'param1:10:param2:20';

        $conditions = Conditions::fromKey($key);
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

    public function testGetSortedParams(): void
    {
        $params = [
            'b' => 2,
            'a' => 1,
            'c' => 3,
        ];

        $conditions = Conditions::create($params);
        $sorted = $conditions->getSortedParams();

        $this->assertCount(3, $sorted);
        $this->assertSame('a', $sorted[0]->name);
        $this->assertSame('b', $sorted[1]->name);
        $this->assertSame('c', $sorted[2]->name);
    }

    public function testGetSortedKey(): void
    {
        $params = [
            'b' => 2,
            'a' => 1,
        ];

        $conditions = Conditions::create($params);

        $this->assertSame('a:1:b:2', $conditions->getSortedKey());
    }

    public function testAllSortedPossibleKeys(): void
    {
        $params = [
            'param1' => 10,
            'param2' => 20,
            'param3' => 30,
        ];

        $expected = [
            'param1:10',
            'param2:20',
            'param3:30',
            'param1:10:param2:20',
            'param1:10:param3:30',
            'param2:20:param3:30',
            'param1:10:param2:20:param3:30',
        ];

        $conditions = Conditions::create($params);
        $actual = $conditions->getAllPossibleSortedKeys();

        sort($expected);
        sort($actual);

        $this->assertSame($expected, $actual);
    }
}
