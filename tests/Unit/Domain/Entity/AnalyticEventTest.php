<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Entity;

use App\Domain\Entity\AnalyticEvent;
use App\Domain\ValueObject\Conditions;
use App\Domain\ValueObject\Event;
use App\Domain\ValueObject\Priority;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AnalyticEvent::class)]
final class AnalyticEventTest extends TestCase
{
    public function testSuccess(): void
    {
        $priority = Priority::create(1);
        $conditions = new Conditions([]);
        $event = Event::create('test_event');

        $analyticEvent = new AnalyticEvent(
            $priority,
            $conditions,
            $event
        );

        $this->assertSame($priority, $analyticEvent->priority);
        $this->assertSame($conditions, $analyticEvent->conditions);
        $this->assertSame($event, $analyticEvent->event);
    }
}
