<?php

namespace App\DataMapper\Mapping\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Table
{
    public function __construct(
        public string $name
    ) {}
}
