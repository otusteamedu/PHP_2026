<?php

declare(strict_types=1);

namespace App\Core\Http\Dto;

use App\Core\Http\InvalidRequestException;

final readonly class CreateMovieRequest
{
    public function __construct(
        public string $title,
        public int $year,
        public string $genre,
        public string $director,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['title'], $data['year'])) {
            throw new InvalidRequestException('Required fields: title, year');
        }

        return new self(
            title: trim((string) $data['title']),
            year: (int) $data['year'],
            genre: trim((string) ($data['genre'] ?? '')),
            director: trim((string) ($data['director'] ?? '')),
        );
    }
}
