<?php

declare(strict_types=1);

namespace App\Core\Http\Dto;

use App\Core\Http\InvalidRequestException;

final readonly class UpdateMovieRequest
{
    public function __construct(
        public ?string $title,
        public ?int $year,
        public ?string $genre,
        public ?string $director,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if ($data === []) {
            throw new InvalidRequestException('Nothing to update');
        }

        $request = new self(
            title: array_key_exists('title', $data) ? trim((string) $data['title']) : null,
            year: array_key_exists('year', $data) ? (int) $data['year'] : null,
            genre: array_key_exists('genre', $data) ? trim((string) $data['genre']) : null,
            director: array_key_exists('director', $data) ? trim((string) $data['director']) : null,
        );

        if (
            $request->title === null
            && $request->year === null
            && $request->genre === null
            && $request->director === null
        ) {
            throw new InvalidRequestException('Allowed fields: title, year, genre, director');
        }

        return $request;
    }
}
