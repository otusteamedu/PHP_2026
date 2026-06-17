<?php

namespace App\Models;

use App\Mappers\PostMapper;

class User
{

    private ?array $posts = null;

    public function __construct(
        private ?int $id,
        private string $first_name,
        private string $last_name,
    ) {}

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setFirstName(): void
    {
        $this->first_name;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setLastName(): void
    {
        $this->last_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getPosts(PostMapper $postMapper): array
    {
        if (is_null($this->posts)) {
            $this->posts = $postMapper->findAllByUserId($this->getId());
        }
        return $this->posts;
    }

    public function setPosts(array $posts): void
    {
        $this->posts = $posts;
    }
}
