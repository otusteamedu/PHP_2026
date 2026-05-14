<?php

declare(strict_types=1);

namespace App\Storage;

use App\Entity\Movie;
use App\Entity\MovieCollection;

interface MovieRepositoryInterface
{
    public function getAll(): MovieCollection;

    public function getById(int $id): ?Movie;

    public function save(Movie $movie): Movie;

    public function deleteById(int $id): bool;
}
