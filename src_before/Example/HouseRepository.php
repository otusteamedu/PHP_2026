<?php

namespace App\Example;

use App\DataMapper\EntityManager;
use App\Example\Infrastucture\Entity\House;
use ArrayObject;
use ReflectionException;

final readonly class HouseRepository
{
    public function __construct(
        private EntityManager $entityManager
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function find(int $id): ?House
    {
        return $this->entityManager->find($id, House::class);
    }

    /**
     * @throws ReflectionException
     */
    public function save(House $house): void
    {
        $this->entityManager->save($house);
    }

    /**
     * @return ArrayObject<House>
     * @throws ReflectionException
     */
    public function findAll(): ArrayObject
    {
        return $this->entityManager->all(House::class);
    }
}
