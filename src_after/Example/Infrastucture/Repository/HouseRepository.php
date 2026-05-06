<?php

namespace App\Example\Infrastucture\Repository;

use App\Example\Domain\Entity\House;
use App\Example\Domain\Repository\HouseRepositoryInterface;

final class HouseRepository extends Repository implements HouseRepositoryInterface
{
    protected string $entityClass = House::class;
}
