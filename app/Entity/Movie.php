<?php

declare(strict_types=1);

namespace App\Entity;

class Movie
{
    public function __construct(
        private string $title,
        private int $year,
        private string $genre,
        private string $director,
        private ?int $id = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function getDirector(): string
    {
        return $this->director;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    public function setDirector(string $director): void
    {
        $this->director = $director;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'year' => $this->year,
            'genre' => $this->genre,
            'director' => $this->director,
        ];
    }
}