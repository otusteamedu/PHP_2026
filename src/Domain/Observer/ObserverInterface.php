<?php

namespace App\Domain\Observer;

/**
 * @template T of Event
 */
interface ObserverInterface
{
    /** @param T $event */
    public function update($event): void;
}
