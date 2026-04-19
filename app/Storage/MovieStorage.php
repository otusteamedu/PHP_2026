<?php

declare(strict_types=1);

namespace App\Storage;

use App\Entity\Movie;
use App\Entity\MovieCollection;
use App\Storage\DataMapper\MovieMapper;

class MovieStorage
{
    private MovieMapper $mapper;

    public function __construct(\PDO $pdo)
    {
        $this->mapper = new MovieMapper($pdo);
    }

    public function getAll(): MovieCollection
    {
        return $this->mapper->findAll();
    }

    public function getById(int $id): ?Movie
    {
        return $this->mapper->findById($id);
    }

    public function create(string $title, int $year, string $genre, string $director): Movie
    {
        $movie = new Movie($title, $year, $genre, $director);
        $this->mapper->insert($movie);

        return $movie;
    }

    public function updateById(int $id, array $fields): ?Movie
    {
        $movie = $this->mapper->findById($id);

        if ($movie === null) {
            return null;
        }

        if (array_key_exists('title', $fields)) {
            $movie->setTitle((string) $fields['title']);
        }
        if (array_key_exists('year', $fields)) {
            $movie->setYear((int) $fields['year']);
        }
        if (array_key_exists('genre', $fields)) {
            $movie->setGenre((string) $fields['genre']);
        }
        if (array_key_exists('director', $fields)) {
            $movie->setDirector((string) $fields['director']);
        }

        $this->mapper->update($movie);

        return $movie;
    }

    public function deleteById(int $id): bool
    {
        return $this->mapper->delete($id);
    }
}