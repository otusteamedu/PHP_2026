<?php

declare(strict_types=1);

namespace App\Entity;

class MovieCollection implements \Countable, \IteratorAggregate
{
    /** @var Movie[] */
    private array $movies = [];

    public function add(Movie $movie): void
    {
        $this->movies[] = $movie;
    }

    public function count(): int
    {
        return count($this->movies);
    }

    /** @return \ArrayIterator<int, Movie> */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->movies);
    }

    public function toArray(): array
    {
        return array_map(static fn(Movie $m) => $m->toArray(), $this->movies);
    }
}