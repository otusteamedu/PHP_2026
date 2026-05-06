<?php

namespace App\Example;

use App\DataMapper\Mapping\Attribute\Column;
use App\DataMapper\Mapping\Attribute\ManyToOne;
use App\DataMapper\Mapping\Attribute\Table;
use App\Example\Infrastucture\Entity\User;

#[Table(name: 'houses')]
final class House
{
    public function __construct(
        #[Column(name: 'id')]
        public ?int $id,
        #[Column(name: 'user_id')]
        public int $userId,
        #[Column(name: 'address')]
        public string $address,
        #[ManyToOne(targetEntity: User::class, localColumn: 'user_id', targetColumn: 'id')]
        public ?User $user = null,
    ) {
    }
}
