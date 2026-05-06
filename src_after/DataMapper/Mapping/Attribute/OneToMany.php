<?php

namespace App\DataMapper\Mapping\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OneToMany
{
    public function __construct(
        public string $targetEntity,
        public string $localColumn = 'id',
        public string $targetColumn = 'id',
    ) {
    }
}
