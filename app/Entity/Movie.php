<?php

declare(strict_types=1);

namespace App\Entity;

use InvalidArgumentException;

final class Movie
{
    private function __construct(
        private string $title,
        private int $year,
        private string $genre,
        private string $director,
        private ?int $id = null,
    ) {
    }

    public static function create(
        string $title,
        int $year,
        string $genre = '',
        string $director = '',
    ): self {
        self::assertValidTitle($title);
        self::assertValidYear($year);

        return new self(
            title: trim($title),
            year: $year,
            genre: trim($genre),
            director: trim($director),
        );
    }

    public static function reconstitute(
        string $title,
        int $year,
        string $genre,
        string $director,
        int $id,
    ): self {
        self::assertValidId($id);
        self::assertValidTitle($title);
        self::assertValidYear($year);

        return new self(
            title: trim($title),
            year: $year,
            genre: trim($genre),
            director: trim($director),
            id: $id,
        );
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
        self::assertValidId($id);

        if ($this->id !== null) {
            throw new InvalidArgumentException('Movie id is already assigned');
        }

        $this->id = $id;
    }

    public function rename(string $title): void
    {
        self::assertValidTitle($title);
        $this->title = trim($title);
    }

    public function changeYear(int $year): void
    {
        self::assertValidYear($year);
        $this->year = $year;
    }

    public function changeGenre(string $genre): void
    {
        $this->genre = trim($genre);
    }

    public function changeDirector(string $director): void
    {
        $this->director = trim($director);
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

    private static function assertValidId(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Movie id must be positive');
        }
    }

    private static function assertValidTitle(string $title): void
    {
        if (trim($title) === '') {
            throw new InvalidArgumentException('Movie title must not be empty');
        }
    }

    private static function assertValidYear(int $year): void
    {
        $maxYear = (int) date('Y') + 1;

        if ($year < 1888 || $year > $maxYear) {
            throw new InvalidArgumentException('Movie year is invalid');
        }
    }
}
