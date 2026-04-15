<?php
namespace App\Entity;
class Post { public function __construct(public int $id, public string $title) {} }
class User {
    private ?array $posts = null;
    public function __construct(private ?int $id, private string $name, private string $email) {}
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function setId(int $id): void { $this->id = $id; }
    public function getPosts($postMapper): array {
        if ($this->posts === null) { $this->posts = $postMapper->findByUserId($this->id); }
        return $this->posts;
    }
}
