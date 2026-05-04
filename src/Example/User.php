<?php

namespace App\Example;

use App\DataMapper\Mapping\Attribute\Column;
use App\DataMapper\Mapping\Attribute\OneToMany;
use App\DataMapper\Mapping\Attribute\OneToOne;
use App\DataMapper\Mapping\Attribute\Table;
use ArrayObject;

#[Table(name: 'users')]
final class User
{
    /**
     * @param ?ArrayObject<Pet> $pets
     */
    public function __construct(
        #[Column(name: 'id')]
        public ?int $id,
        #[Column(name: 'name')]
        public string $name,
        #[Column(name: 'email')]
        public string $email,
        #[OneToOne(targetEntity: Profile::class, localColumn: 'id', targetColumn: 'user_id')]
        public ?Profile $profile = null,
        #[OneToMany(targetEntity: Pet::class, localColumn: 'id', targetColumn: 'user_id')]
        public ?ArrayObject $pets = null,
    ) {
    }
}
