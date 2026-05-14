<?php

declare(strict_types=1);

namespace App\Storage;

use App\Entity\Movie;
use App\Entity\MovieCollection;
use App\Storage\DataMapper\MovieMapper;

final readonly class PdoMovieRepository implements MovieRepositoryInterface
{
    public function __construct(
        private MovieMapper $mapper,
    ) {
    }

    public function getAll(): MovieCollection
    {
        return $this->mapper->findAll();
    }

    public function getById(int $id): ?Movie
    {
        return $this->mapper->findById($id);
    }

    public function save(Movie $movie): Movie
    {
        if ($movie->getId() === null) {
            $this->mapper->insert($movie);
        } else {
            $this->mapper->update($movie);
        }

        return $movie;
    }

    public function deleteById(int $id): bool
    {
        return $this->mapper->delete($id);
    }
}
