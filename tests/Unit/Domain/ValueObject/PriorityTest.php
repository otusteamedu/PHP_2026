<?php

declare(strict_types=1);

namespace Test\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Priority;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Priority::class)]
final class PriorityTest extends TestCase
{
    public function testSuccess(): void
    {
        $priority = Priority::create($value = 10);

        $this->assertSame($value, $priority->value);
    }
}
