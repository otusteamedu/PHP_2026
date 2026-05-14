<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Http\Dto\CreateMovieRequest;
use App\Core\Http\Dto\UpdateMovieRequest;
use App\Entity\Movie;
use App\Entity\MovieCollection;
use App\Storage\MovieRepositoryInterface;

final readonly class MovieService
{
    public function __construct(
        private MovieRepositoryInterface $movies,
    ) {
    }

    public function getAll(): MovieCollection
    {
        return $this->movies->getAll();
    }

    public function getById(int $id): ?Movie
    {
        return $this->movies->getById($id);
    }

    public function create(CreateMovieRequest $request): Movie
    {
        return $this->movies->save(Movie::create(
            title: $request->title,
            year: $request->year,
            genre: $request->genre,
            director: $request->director,
        ));
    }

    public function update(int $id, UpdateMovieRequest $request): ?Movie
    {
        $movie = $this->movies->getById($id);

        if ($movie === null) {
            return null;
        }

        if ($request->title !== null) {
            $movie->rename($request->title);
        }
        if ($request->year !== null) {
            $movie->changeYear($request->year);
        }
        if ($request->genre !== null) {
            $movie->changeGenre($request->genre);
        }
        if ($request->director !== null) {
            $movie->changeDirector($request->director);
        }

        return $this->movies->save($movie);
    }

    public function deleteById(int $id): bool
    {
        return $this->movies->deleteById($id);
    }
}
