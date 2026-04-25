<?php

declare(strict_types=1);

namespace Test\Unit\Domain\ValueObject;

use App\Domain\ValueObject\Event;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

#[CoversClass(Event::class)]
final class EventTest extends TestCase
{
    public function testSuccess(): void
    {
        $event = Event::create($value = 'test_event');

        $this->assertSame($value, $event->value);
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Event::create('');
    }
}
