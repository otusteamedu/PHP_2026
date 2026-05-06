<?php

namespace App\Example;

use App\DataMapper\EntityManager;
use App\Example\Infrastucture\Entity\UserPet;
use ArrayObject;
use ReflectionException;

final readonly class UserPetRepository
{
    public function __construct(
        private EntityManager $entityManager
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function find(int $id): ?UserPet
    {
        return $this->entityManager->find($id, UserPet::class);
    }

    /**
     * @throws ReflectionException
     */
    public function save(UserPet $userPet): void
    {
        $this->entityManager->save($userPet);
    }

    /**
     * @return ArrayObject<UserPet>
     * @throws ReflectionException
     */
    public function findAll(): ArrayObject
    {
        return $this->entityManager->all(UserPet::class);
    }
}
