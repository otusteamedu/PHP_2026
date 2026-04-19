<?php

declare(strict_types=1);

namespace App\Storage\DataMapper;

use App\Entity\Movie;

class IdentityMap
{
    /** @var array<int, Movie> */
    private array $map = [];

    public function get(int $id): ?Movie
    {
        return $this->map[$id] ?? null;
    }

    public function set(Movie $movie): void
    {
        if ($movie->getId() !== null) {
            $this->map[$movie->getId()] = $movie;
        }
    }

    public function has(int $id): bool
    {
        return isset($this->map[$id]);
    }

    public function remove(int $id): void
    {
        unset($this->map[$id]);
    }
}
