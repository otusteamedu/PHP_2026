<?php

class UserRepository
{
    public function __construct(
        private EntityManager $em
    ) {}

    public function find(int $id): ?User
    {
        return $this->em->find(UserMetadata::class, $id, User::class);
    }

    public function save(User $user): void
    {
        $this->em->save($user, UserMetadata::class);
    }

    public function uptadeWithCondition($user, $condition): void
    {
        $sql = "UPDATE `users` SET `condition` = :condition WHERE `id` = :id";
    }
}