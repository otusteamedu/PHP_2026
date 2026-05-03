<?php

declare(strict_types=1);

namespace App\DataMapper;

use ArrayIterator;
use ArrayObject;

/**
 * @template T
 * @extends ArrayObject<int, T>
 */
class EntityCollection extends ArrayObject
{
    public function __construct(array $entities = [])
    {
        parent::__construct($entities, 0, ArrayIterator::class);
    }
}
