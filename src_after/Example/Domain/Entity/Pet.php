<?php

namespace App\Example\Domain\Entity;

use App\DataMapper\Mapping\Attribute\Column;
use App\DataMapper\Mapping\Attribute\Table;

#[Table(name: 'pets')]
final class Pet
{
    public function __construct(
        #[Column(name: 'id')]
        public ?int $id,
        #[Column(name: 'type')]
        public string $type,
    ) {
    }
}
