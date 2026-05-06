<?php

namespace App\Example\Infrastucture\Repository;

use App\Example\Domain\Entity\User;
use App\Example\Domain\Repository\UserRepositoryInterface;
use ArrayObject;
use ReflectionException;

final class UserRepository extends Repository implements UserRepositoryInterface
{
    protected string $entityClass = User::class;

    /**
     * @throws ReflectionException
     */
    public function findByEmail(string $email): ArrayObject
    {
        return $this->entityManager->all(User::class, ['email' => $email]);
    }
}
