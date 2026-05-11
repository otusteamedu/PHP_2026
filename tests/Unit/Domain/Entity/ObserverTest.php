<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Entity;

use App\Domain\Entity\Burger;
use PHPUnit\Framework\TestCase;
use SplObserver;

final class ObserverTest extends TestCase
{
    public function testSubjectObserverFunctions(): void
    {
        $subject = new Burger('Cheeseburger');
        $observer = $this->createMock(SplObserver::class);
        $observer->expects(self::once())->method('update');
        $subject->attach($observer);
        $subject->notify();
        $subject->detach($observer);
        $subject->notify();
    }

    public function testNotifiedWhenCooked(): void
    {
        $subject = new Burger('Cheeseburger');
        $observer = $this->createMock(SplObserver::class);
        $observer->expects(self::once())->method('update')->with(self::equalTo($subject));

        $subject->attach($observer);

        $subject->cook();
    }
}
