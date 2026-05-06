<?php

namespace App\Example;

use App\DataMapper\EntityManager;
use ArrayObject;
use ReflectionException;

final readonly class ProfileRepository
{
    public function __construct(
        private EntityManager $entityManager
    ) {}

    /**
     * @throws ReflectionException
     */
    public function find(int $id): ?Profile
    {
        return $this->entityManager->find($id, Profile::class);
    }

    /**
     * @throws ReflectionException
     */
    public function save(Profile $Profile): void
    {
        $this->entityManager->save($Profile);
    }

    /**
     * @return ArrayObject<Profile>
     * @throws ReflectionException
     */
    public function findAll(): ArrayObject
    {
        return $this->entityManager->all(Profile::class);
    }
}
