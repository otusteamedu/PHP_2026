<?php

namespace App\Example\Domain\Entity;

use App\DataMapper\Mapping\Attribute\Column;
use App\DataMapper\Mapping\Attribute\Table;

#[Table(name: 'user_pets')]
final class UserPet
{
    public function __construct(
        #[Column(name: 'id')]
        public ?int $id,
        #[Column(name: 'user_id')]
        public int $userId,
        #[Column(name: 'pet_id')]
        public int $petId,
    ) {
    }
}
