<?php

namespace App\Example;

use App\DataMapper\Mapping\Attribute\Column;
use App\DataMapper\Mapping\Attribute\Table;

#[Table(name: 'pets')]
final class Pet
{
    public function __construct(
        #[Column(name: 'id')]
        public ?int $id,
        #[Column(name: 'user_id')]
        public int $userId,
        #[Column(name: 'type')]
        public string $type,
    ) {
    }
}
