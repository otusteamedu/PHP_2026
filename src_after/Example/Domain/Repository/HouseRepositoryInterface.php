<?php

namespace App\Example\Domain\Repository;

use App\Example\Domain\Entity\House;

/**
 * @template-implements RepositoryInterface<House>
 */
interface HouseRepositoryInterface extends RepositoryInterface
{
}
