<?php

namespace App\Example;

use App\DataMapper\EntityManager;
use ArrayObject;
use ReflectionException;

final readonly class UserRepository
{
    public function __construct(
        private EntityManager $entityManager
    ) {}

    /**
     * @throws ReflectionException
     */
    public function find(int $id): ?User
    {
        return $this->entityManager->find($id, User::class);
    }

    /**
     * @throws ReflectionException
     */
    public function save(User $user): void
    {
        $this->entityManager->save($user);
    }

    /**
     * @return ArrayObject<User>
     * @throws ReflectionException
     */
    public function findAll(): ArrayObject
    {
        return $this->entityManager->all(User::class);
    }

    /**
     * @return ArrayObject<User>
     * @throws ReflectionException
     */
    public function findByEmail(string $email): ArrayObject
    {
        return $this->entityManager->all(User::class, ['email' => $email]);
    }
}
