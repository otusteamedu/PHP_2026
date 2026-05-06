<?php

namespace App\Example\Infrastucture\Repository;

use App\Example\Domain\Entity\Profile;
use App\Example\Domain\Repository\ProfileRepositoryInterface;

final class ProfileRepository extends Repository implements ProfileRepositoryInterface
{
    protected string $entityClass = Profile::class;
}
