<?php

namespace App\DataMapper\Mapping\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ManyToMany
{
    public function __construct(
        public string $targetEntity,
        public string $joinEntity,
        public string $localColumn = 'id',
        public string $joinLocalColumn = 'id',
        public string $joinTargetColumn = 'id',
        public string $targetColumn = 'id',
    ) {
    }
}
