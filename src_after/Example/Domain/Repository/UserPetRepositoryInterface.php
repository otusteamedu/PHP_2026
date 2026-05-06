<?php

namespace App\Example\Domain\Repository;

use App\Example\Domain\Entity\UserPet;

/**
 * @template-implements RepositoryInterface<UserPet>
 */
interface UserPetRepositoryInterface extends RepositoryInterface
{
}
