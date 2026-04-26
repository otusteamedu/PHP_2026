<?php

namespace App\DataMapper;

use App\DataMapper\Mapping\MetadataReader;
use PDO;
use ReflectionException;

class EntityManager
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly MetadataReader $metadataReader,
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function save(object $entity): void
    {
        $table = $this->metadataReader->getTableName($entity::class);
        $fields = $this->metadataReader->getMapping($entity::class);

        $data = [];
        foreach ($fields as $prop => $column) {
            $data[$column] = $entity->$prop;
        }

        if ($entity->id === null) {
            // INSERT
            unset($data['id']);

            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_map(fn($c) => ":$c", array_keys($data)));

            $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);

            $entity->id = (int)$this->pdo->lastInsertId();
        } else {
            // UPDATE
            $set = implode(', ', array_map(fn($c) => "$c = :$c", array_keys($data)));

            $sql = "UPDATE $table SET $set WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
        }
    }

    public function find(int $id, string $entityClass): ?object
    {
        $table = $this->metadataReader->getTableName($entityClass);
        $fields = $this->metadataReader->getMapping($entityClass);

        $sql = "SELECT * FROM $table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $args = [];
        foreach ($fields as $prop => $column) {
            $args[$prop] = $row[$column];
        }

        return new $entityClass(...$args);
    }
}
