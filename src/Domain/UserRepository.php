<?php

namespace App\Domain;

use App\DataMapper\EntityCollection;
use App\DataMapper\EntityManager;
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
     * @throws ReflectionException
     */
    public function findAll(): EntityCollection
    {
        return $this->entityManager->all(User::class);
    }

    /**
     * @throws ReflectionException
     */
    public function findByEmail(string $email): UserCollection
    {
        $users = $this->entityManager->all(User::class, ['email' => $email]);
        return new UserCollection((array) $users);
    }
}
