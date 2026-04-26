<?php

namespace App\DataMapper;

final readonly class UserRepository
{
    public function __construct(
        private EntityManager $entityManager
    ) {}

    public function find(int $id): ?User
    {
        return $this->entityManager->find($id, User::class);
    }

    public function save(User $user): void
    {
        $this->entityManager->save($user);
    }
}
