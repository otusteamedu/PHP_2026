<?php

namespace App\Example\Domain\Repository;

use App\Example\Domain\Entity\Pet;

/**
 * @template-implements RepositoryInterface<Pet>
 */
interface PetRepositoryInterface extends RepositoryInterface
{}
