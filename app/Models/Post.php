<?php

namespace App\Models;

class Post
{
    public function __construct(
        private ?int $id,
        private int $user_id,
        private string $content,
        private string $created_at,
        private string $updated_at
    ) {}

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUserId(int $id): void
    {
        $this->user_id = $id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->created_at = $createdAt;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updated_at = $updatedAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }
}
