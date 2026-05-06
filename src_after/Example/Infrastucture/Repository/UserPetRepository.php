<?php

namespace App\Example\Infrastucture\Repository;

use App\Example\Domain\Entity\UserPet;
use App\Example\Domain\Repository\UserPetRepositoryInterface;

final class UserPetRepository extends Repository implements UserPetRepositoryInterface
{
    protected string $entityClass = UserPet::class;
}
