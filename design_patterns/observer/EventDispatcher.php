<?php
declare (strict_types = 1);

class EventDispatcher
{
    private array $listeners = [];

    public function listen(string $eventClass, ListenerInterface $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    public function dispatch(object $event)
    {
        $eventClass = get_class($event);

        $listenerList = $this->listeners[$eventClass] ?? [];

        foreach ($listenerList as $listener) {
            $listener->handle($event);
        }
    }
}
