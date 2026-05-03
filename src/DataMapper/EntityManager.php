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
            $data[$column] = $entity->{$prop};
        }

        if ($entity->id === null) {
            unset($data['id']);
            $columns = $placeholders = [];
            foreach (array_keys($data) as $column) {
                $columns[] = $column;
                $placeholders[] = ":{$column}";
            }

            $columns = implode(', ', $columns);
            $placeholders = implode(', ', $placeholders);

            $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);

            $entity->id = (int)$this->pdo->lastInsertId();
        } else {
            $set = $data
                    |> array_keys(...)
                    |> (fn($columns) => array_map(fn($c) => "$c = :$c", $columns))
                    |> (fn($bindColumns) => implode(', ', $bindColumns));

            $sql = "UPDATE $table SET $set WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
        }
    }

    /**
     * @throws ReflectionException
     */
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

        $args = array_map(fn ($column) => $row[$column], $fields);

        return new $entityClass(...$args);
    }

    public function all(string $entityClass, array $conditions = []): EntityCollection
    {
        $table = $this->metadataReader->getTableName($entityClass);
        $fields = $this->metadataReader->getMapping($entityClass);

        $sql = "SELECT * FROM $table";

        $safeConditions = array_intersect_key($conditions, $fields);
        $bindings = [];
        foreach ($safeConditions as $column => $value) {
            $bindings[] = "$column = :{$column}";
        }

        if (!empty($bindings)) {
            $sql .= ' WHERE ' . implode(' AND ', $bindings);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($conditions);
        $rows = $stmt->fetchAll();

        $entities = array_map(
            function ($row) use ($fields, $entityClass) {
                $args = array_map(fn ($column)  => $row[$column], $fields);
                return new $entityClass(...$args);
            },
            $rows
        );

        return new EntityCollection($entities);
    }
}
