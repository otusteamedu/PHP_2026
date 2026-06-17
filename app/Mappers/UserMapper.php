<?php

namespace App\Mappers;

use App\Models\User;
use PDO;

class UserMapper
{
    private PDO $db;
    private array $identityMap = [];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Массовое получение всех пользователей
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM users");
        $results = $stmt->fetchAll();

        $users = [];
        foreach ($results as $row) {
            $user = $this->createUserFromRow($row);
            $this->identityMap[$user->getId()] = $user;
            $users[] = $user;
        }
        return $users;
    }

    // Получение пользователей с пагинацией
    public function paginate(?int $limit = null, int $page = 1): array
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM users');
        $totalCount = $stmt->fetchColumn();

        if (is_null($limit)) {
            $limit = $totalCount;
        }

        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("SELECT * FROM users LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $users = [];
        foreach ($results as $row) {
            $user = $this->createUserFromRow($row);
            $this->identityMap[$user->getId()] = $user;
            $users[] = $user;
        }
        return [
            'items' => $users,
            'meta' => [
                'total' => $totalCount,
                'current_page' => $page,
                'last_page' => (int)ceil($totalCount / $limit),
                'per_page' => $limit,
            ]
        ];
    }

    // Получение пользователя по ID
    public function findById(int $id): ?User
    {
        if (isset($this->identityMap[$id])) return $this->identityMap[$id];

        var_dump('Ищем в базе');
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->createUserFromRow($row);
    }

    // Сохранение (вставка или обновление)
    public function save(User $user): void
    {
        if ($user->getId()) {
            $this->update($user);
        } else {
            $this->insert($user);
        }
    }

    private function insert(User $user): void
    {
        $sql = "INSERT INTO users (first_name, last_name) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $user->getFirstName(),
            $user->getLastName()
        ]);

        $lastInsertId = (int)$this->db->lastInsertId();

        $user->setId($lastInsertId);

        $this->identityMap[$user->getId()] = $user;
    }

    private function update(User $user): void
    {
        $sql = "UPDATE users SET first_name = ?, last_name = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $user->getFirstName(),
            $user->getLastName(),
            $user->getId()
        ]);

        $this->identityMap[$user->getId()] = $user;
    }

    // Удаление
    public function delete(User $user): void
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user->getId()]);

        unset($this->identityMap[$user->getId()]);
    }

    // Вспомогательный метод: создание объекта из строки БД
    private function createUserFromRow(array $row): User
    {
        $user = new User($row['id'], $row['first_name'], $row['last_name']);
        return $user;
    }

    protected function loadPosts(User $user): void
    {
        $postMapper = new PostMapper($this->db);
        $posts = $postMapper->findAllByUserId($user->getId());
        $user->setPosts($posts);
    }
}
