<?php

declare(strict_types=1);

namespace App\DataMapper;

use ArrayObject;
use Closure;
use Iterator;

class LazyEntityCollection extends ArrayObject
{
    private bool $initialized = false;

    public function __construct(private ?Closure $loader)
    {
        parent::__construct();
    }

    private function initialize(): void
    {
        if (!$this->initialized && $this->loader !== null) {
            $loaded = ($this->loader)();

            $this->exchangeArray($loaded->getArrayCopy());

            $this->initialized = true;
            $this->loader = null;
        }
    }

    public function getIterator(): Iterator
    {
        $this->initialize();
        return parent::getIterator();
    }

    public function count(): int
    {
        $this->initialize();
        return parent::count();
    }

    public function offsetGet(mixed $key): mixed
    {
        $this->initialize();
        return parent::offsetGet($key);
    }

    public function offsetExists(mixed $key): bool
    {
        $this->initialize();
        return parent::offsetExists($key);
    }

}
