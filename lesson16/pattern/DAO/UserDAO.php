<?php

class UserDAO
{
    public function __construct(private PDO $pdo)
    { }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT id, username, created_at FROM users WHERE id = :id");

        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new User(
            id: (int)$row['id'],
            username: $row['username'],
            createdAt: $row['created_at']
        );
    }

    public function create(User $user): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (username) VALUES (:username)");

        $stmt->execute([':username' => $user->username,]);

        return (int)$this->pdo->lastInsertId();
    }
}