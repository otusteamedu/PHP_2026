<?php

namespace App\Example;

use App\DataMapper\Mapping\Attribute\Column;
use App\DataMapper\Mapping\Attribute\ManyToMany;
use App\DataMapper\Mapping\Attribute\OneToMany;
use App\DataMapper\Mapping\Attribute\OneToOne;
use App\DataMapper\Mapping\Attribute\Table;
use App\Example\Infrastucture\Entity\House;
use App\Example\Infrastucture\Entity\Pet;
use App\Example\Infrastucture\Entity\Profile;
use App\Example\Infrastucture\Entity\UserPet;
use ArrayObject;

#[Table(name: 'users')]
final class User
{
    /**
     * @param ?ArrayObject<Pet> $pets
     * @param ?ArrayObject<House> $houses
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
        #[ManyToMany(
            targetEntity: Pet::class,
            joinEntity: UserPet::class,
            localColumn: 'id',
            joinLocalColumn: 'user_id',
            joinTargetColumn: 'pet_id',
            targetColumn: 'id'
        )]
        public ?ArrayObject $pets = null,
        #[OneToMany(targetEntity: House::class, localColumn: 'id', targetColumn: 'user_id')]
        public ?ArrayObject $houses = null,
    ) {
    }
}
