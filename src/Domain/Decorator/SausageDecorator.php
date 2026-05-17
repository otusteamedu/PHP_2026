<?php

declare(strict_types=1);

namespace App\Domain\Decorator;

final readonly class SausageDecorator extends Decorator
{
    public function prepare(): string
    {
        return parent::prepare() . ' + сосиска';
    }
}
