<?php

declare(strict_types=1);

namespace App\Storage\DataMapper;

use App\Entity\Movie;
use App\Entity\MovieCollection;

class MovieMapper
{
    public function __construct(
        private readonly \PDO $pdo,
        private readonly IdentityMap $identityMap,
    ) {
    }

    public function findById(int $id): ?Movie
    {
        // Return cached instance if already loaded this request
        if ($this->identityMap->has($id)) {
            return $this->identityMap->get($id);
        }

        $stmt = $this->pdo->prepare('SELECT * FROM movies WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $movie = $this->hydrate($row);
        $this->identityMap->set($movie);

        return $movie;
    }

    /**
     * Bulk fetch — returns all movies ordered by id as a typed collection.
     */
    public function findAll(): MovieCollection
    {
        $stmt = $this->pdo->query('SELECT * FROM movies ORDER BY id');
        $collection = new MovieCollection();

        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $id = (int) $row['id'];

            if ($this->identityMap->has($id)) {
                $collection->add($this->identityMap->get($id));
                continue;
            }

            $movie = $this->hydrate($row);
            $this->identityMap->set($movie);
            $collection->add($movie);
        }

        return $collection;
    }

    public function insert(Movie $movie): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO movies (title, year, genre, director)
             VALUES (:title, :year, :genre, :director)
             RETURNING id'
        );

        $stmt->execute([
            'title'    => $movie->getTitle(),
            'year'     => $movie->getYear(),
            'genre'    => $movie->getGenre(),
            'director' => $movie->getDirector(),
        ]);

        $movie->setId((int) $stmt->fetchColumn());
        $this->identityMap->set($movie);
    }

    public function update(Movie $movie): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE movies
             SET title = :title, year = :year, genre = :genre, director = :director
             WHERE id = :id'
        );

        $stmt->execute([
            'id'       => $movie->getId(),
            'title'    => $movie->getTitle(),
            'year'     => $movie->getYear(),
            'genre'    => $movie->getGenre(),
            'director' => $movie->getDirector(),
        ]);

        if ($stmt->rowCount() > 0) {
            $this->identityMap->set($movie);
            return true;
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM movies WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $this->identityMap->remove($id);

        return $stmt->rowCount() > 0;
    }

    /**
     * Hydrate a Movie entity from a raw database row.
     */
    private function hydrate(array $row): Movie
    {
        return Movie::reconstitute(
            title:    $row['title'],
            year:     (int) $row['year'],
            genre:    $row['genre'],
            director: $row['director'],
            id:       (int) $row['id'],
        );
    }
}
