<?php

declare (strict_types = 1);

interface ListenerInterface
{
    public function handle(object $event): void;
}
