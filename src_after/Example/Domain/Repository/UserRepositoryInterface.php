<?php

namespace App\Example\Domain\Repository;

use App\Example\Domain\Entity\User;
use ArrayObject;

/**
 * @template-implements RepositoryInterface<User>
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * @return ArrayObject<User>
     */
    public function findByEmail(string $email): ArrayObject;
}
