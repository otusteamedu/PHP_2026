<?php

class EntityManager
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function save(object $entity, string $metadataClass): void
    {
        $table = $metadataClass::table();
        $fields = $metadataClass::fields();

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

    public function find(string $metadataClass, int $id, string $entityClass): ?object
    {
        $table = $metadataClass::table();
        $fields = $metadataClass::fields();

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
