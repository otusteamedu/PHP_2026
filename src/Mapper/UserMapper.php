<?php
namespace App\Mapper;
use App\Entity\User;
use App\Entity\Post;
use PDO;
class UserMapper {
    private array $identityMap = [];
    public function __construct(private PDO $pdo) {}
    private function mapRowToEntity(array $row): User {
        $id = (int)$row["id"];
        if (!isset($this->identityMap[$id])) { $this->identityMap[$id] = new User($id, $row["name"], $row["email"]); }
        return $this->identityMap[$id];
    }
    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM users");
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $users[] = $this->mapRowToEntity($row); }
        return $users;
    }
    public function findById(int $id): ?User {
        if (isset($this->identityMap[$id])) return $this->identityMap[$id];
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRowToEntity($row) : null;
    }
}
class PostMapper {
    public function __construct(private PDO $pdo) {}
    public function findByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE user_id = ?");
        $stmt->execute([$userId]);
        $posts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { $posts[] = new Post((int)$row["id"], $row["title"]); }
        return $posts;
    }
}
