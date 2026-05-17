<?php

namespace App\Domain\Observer;

use App\Domain\Observer\Event\Event;

/**
 * @template T of Event
 */
interface ObserverInterface
{
    /** @param T $event */
    public function update($event): void;
}
