<?php

namespace App\Mappers;

use App\Models\Post;
use PDO;

class PostMapper
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Массовое получение всех пользователей
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM posts");
        $results = $stmt->fetchAll();

        $posts = [];
        foreach ($results as $row) {
            $posts[] = $this->createPostFromRow($row);
        }
        return $posts;
    }

    // Получение пост по ID
    public function findById(int $id): ?Post
    {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->createPostFromRow($row);
    }

    // Получение пост по ID пользователя
    public function findAllByUserId(int $userId): array
    {

        $stmt = $this->db->prepare("SELECT * FROM posts WHERE `user_id` = ?");
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll();

        $posts = [];
        foreach ($results as $row) {
            $posts[] = $this->createPostFromRow($row);
        }
        return $posts;
    }

    // Сохранение (вставка или обновление)
    public function save(Post $post): void
    {
        if ($post->getId()) {
            $this->update($post);
        } else {
            $this->insert($post);
        }
    }

    private function insert(Post $post): void
    {
        $sql = "INSERT INTO posts (user_id, content, created_at, updated_at) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $post->getUserId(),
            $post->getContent(),
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
        ]);

        // Устанавливаем ID после вставки
        $post->setId((int)$this->db->lastInsertId());
    }

    private function update(Post $post): void
    {
        $sql = "UPDATE posts SET user_id = ?, content = ?, updated_at = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $post->getUserId(),
            $post->getContent(),
            date('Y-m-d H:i:s'),
            $post->getId()
        ]);
    }

    // Удаление
    public function delete(Post $post): void
    {
        $sql = "DELETE FROM posts WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$post->getId()]);
    }

    // Вспомогательный метод: создание объекта из строки БД
    private function createPostFromRow(array $row): Post
    {
        $post = new Post($row['id'], $row['user_id'], $row['content'], $row['created_at'], $row['updated_at']);
        return $post;
    }
}
