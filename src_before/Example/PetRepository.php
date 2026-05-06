<?php

namespace App\Example;

use App\DataMapper\EntityManager;
use App\Example\Infrastucture\Entity\Pet;
use ArrayObject;
use ReflectionException;

final readonly class PetRepository
{
    public function __construct(
        private EntityManager $entityManager
    ) {}

    /**
     * @throws ReflectionException
     */
    public function find(int $id): ?Pet
    {
        return $this->entityManager->find($id, Pet::class);
    }

    /**
     * @throws ReflectionException
     */
    public function save(Pet $Pet): void
    {
        $this->entityManager->save($Pet);
    }

    /**
     * @return ArrayObject<Pet>
     * @throws ReflectionException
     */
    public function findAll(): ArrayObject
    {
        return $this->entityManager->all(Pet::class);
    }
}
