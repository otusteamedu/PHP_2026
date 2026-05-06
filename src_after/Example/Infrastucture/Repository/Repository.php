<?php

declare(strict_types=1);

namespace App\Example\Infrastucture\Repository;

use App\DataMapper\EntityManager;
use App\Example\Domain\Repository\RepositoryInterface;
use ArrayObject;
use ReflectionException;

abstract class Repository implements RepositoryInterface
{
    public function __construct(
        protected EntityManager $entityManager,
        protected string $entityClass
    ) {}

    /**
     * @throws ReflectionException
     */
    public function find(int $id): ?object
    {
        return $this->entityManager->find($id, $this->entityClass);
    }

    /**
     * @throws ReflectionException
     */
    public function findAll(): ArrayObject
    {
        return $this->entityManager->all($this->entityClass);
    }

    /**
     * @throws ReflectionException
     */
    public function save(object $object): void
    {
        $this->entityManager->save($object);
    }
}
