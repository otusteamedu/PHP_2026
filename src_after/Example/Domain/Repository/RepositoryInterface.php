<?php

namespace App\Example\Domain\Repository;

use ArrayObject;

/**
 * @template T of object
 */
interface RepositoryInterface
{
    /**
     * @return T|null
     */
    public function find(int $id): ?object;

    /**
     * @param T $object
     */
    public function save(object $object): void;

    /**
     * @return ArrayObject<T>
     */
    public function findAll(): ArrayObject;
}
