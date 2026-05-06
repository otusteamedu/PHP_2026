<?php

namespace App\Example\Infrastucture\Repository;

use App\Example\Domain\Entity\Pet;
use App\Example\Domain\Repository\PetRepositoryInterface;

final class PetRepository extends Repository implements PetRepositoryInterface
{
    protected string $entityClass = Pet::class;
}
