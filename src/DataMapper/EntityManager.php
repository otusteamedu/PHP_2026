<?php

namespace App\DataMapper;

use App\DataMapper\Mapping\MetadataReader;
use ArrayObject;
use PDO;
use ReflectionClass;
use ReflectionException;

class EntityManager
{
    /**
     * @var array<string, object>
     */
    private array $identityMap = [];

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

            $entity->id = (int) $this->pdo->lastInsertId();
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

        $identityMapId = $this->buildIdentityMapId($entityClass, $id);
        if ($entity = $this->getFromIdentityMap($identityMapId)) {
            return $entity;
        }

        $sql = "SELECT * FROM $table WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $entity = $this->getEntity($entityClass, $fields, $row);
        $this->setToIdentityMap($identityMapId, $entity);

        return $entity;
    }

    /**
     * @throws ReflectionException
     */
    public function all(string $entityClass, array $conditions = []): ArrayObject
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

        return $this->getEntities($entityClass, $fields, $rows);
    }

    /**
     * @param array<string, mixed> $row
     * @throws ReflectionException
     */
    private function hydrateRelations(object $entity, string $entityClass, array $row): void
    {
        $relations = $this->metadataReader->getRelations($entityClass);
        foreach ($relations as $property => $relation) {
            $localValue = $row[$relation['localColumn']] ?? null;
            if ($localValue === null) {
                $entity->{$property} = null;
                continue;
            }

            $entity->{$property} = $this->createOneToOneRelation(
                $relation['targetEntity'],
                $relation['targetColumn'],
                $localValue
            );
        }

        $toManyRelations = $this->metadataReader->getToManyRelations($entityClass);
        foreach ($toManyRelations as $property => $relation) {
            $localValue = $row[$relation['localColumn']] ?? null;
            if ($localValue === null) {
                $entity->{$property} = [];
                continue;
            }

            $entity->{$property} = $this->findAllByColumn(
                $relation['targetEntity'],
                $relation['targetColumn'],
                $localValue
            );
        }

        $manyToOneRelations = $this->metadataReader->getManyToOneRelations($entityClass);
        foreach ($manyToOneRelations as $property => $relation) {
            $localValue = $row[$relation['localColumn']] ?? null;
            if ($localValue === null) {
                $entity->{$property} = null;
                continue;
            }

            $entity->{$property} = $this->createManyToOneRelation(
                $relation['targetEntity'],
                $relation['targetColumn'],
                $localValue
            );
        }

        $manyToManyRelations = $this->metadataReader->getManyToManyRelations($entityClass);
        foreach ($manyToManyRelations as $property => $relation) {
            $localValue = $row[$relation['localColumn']] ?? null;
            if ($localValue === null) {
                $entity->{$property} = new ArrayObject([]);
                continue;
            }

            $entity->{$property} = $this->findAllByManyToMany(
                $relation['targetEntity'],
                $relation['joinEntity'],
                $relation['targetColumn'],
                $relation['joinLocalColumn'],
                $relation['joinTargetColumn'],
                $localValue
            );
        }
    }

    /**
     * @throws ReflectionException
     */
    private function createOneToOneRelation(string $targetClass, string $targetColumn, mixed $localValue): object
    {
        $initializer = fn (): ?object => $this->findOneByColumn($targetClass, $targetColumn, $localValue);

        return new ReflectionClass($targetClass)->newLazyGhost(function (object $ghost) use ($initializer, $targetClass): void {
            $loaded = $initializer();
            if ($loaded === null) {
                return;
            }

            foreach (get_object_vars($loaded) as $property => $value) {
                $ghost->{$property} = $value;
            }

            $mapping = $this->metadataReader->getMapping($targetClass);
            $row = [];
            foreach ($mapping as $property => $column) {
                $row[$column] = $loaded->{$property};
            }
            $this->hydrateRelations($ghost, $targetClass, $row);
        });
    }

    /**
     * @throws ReflectionException
     */
    private function createManyToOneRelation(string $targetClass, string $targetColumn, mixed $localValue): object
    {
        $initializer = fn (): ?object => $this->findOneByColumn($targetClass, $targetColumn, $localValue);

        return new ReflectionClass($targetClass)->newLazyGhost(function (object $ghost) use ($initializer, $targetClass): void {
            $loaded = $initializer();
            if ($loaded === null) {
                return;
            }

            foreach (get_object_vars($loaded) as $property => $value) {
                $ghost->{$property} = $value;
            }

            $mapping = $this->metadataReader->getMapping($targetClass);
            $row = [];
            foreach ($mapping as $property => $column) {
                $row[$column] = $loaded->{$property};
            }
            $this->hydrateRelations($ghost, $targetClass, $row);
        });
    }

    /**
     * @throws ReflectionException
     */
    private function findOneByColumn(string $entityClass, string $column, mixed $value): ?object
    {
        $table = $this->metadataReader->getTableName($entityClass);
        $fields = $this->metadataReader->getMapping($entityClass);

        $sql = "SELECT * FROM $table WHERE $column = :value LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['value' => $value]);

        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $id = null;
        $idColumn = $fields['id'] ?? null;
        if ($idColumn !== null) {
            $id = (int) $row[$idColumn];
            $identityMapId = $this->buildIdentityMapId($entityClass, $id);
            if ($entity = $this->getFromIdentityMap($identityMapId)) {
                return $entity;
            }
        }

        $entity = $this->getEntity($entityClass, $fields, $row);

        if ($id !== null) {
            $this->setToIdentityMap($this->buildIdentityMapId($entityClass, $id), $entity);
        }

        return $entity;
    }

    /**
     * @throws ReflectionException
     */
    private function findAllByColumn(string $entityClass, string $column, mixed $value): ArrayObject
    {
        $table = $this->metadataReader->getTableName($entityClass);
        $fields = $this->metadataReader->getMapping($entityClass);

        $sql = "SELECT * FROM $table WHERE $column = :value";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['value' => $value]);
        $rows = $stmt->fetchAll();

        return $this->getEntities($entityClass, $fields, $rows);
    }

    /**
     * @throws ReflectionException
     */
    private function findAllByManyToMany(
        string $targetEntityClass,
        string $joinEntityClass,
        string $targetColumn,
        string $joinLocalColumn,
        string $joinTargetColumn,
        mixed $localValue
    ): ArrayObject {
        $targetTable = $this->metadataReader->getTableName($targetEntityClass);
        $targetFields = $this->metadataReader->getMapping($targetEntityClass);
        $joinTable = $this->metadataReader->getTableName($joinEntityClass);

        $sql = "SELECT t.* FROM $targetTable t "
             . "INNER JOIN $joinTable j ON t.$targetColumn = j.$joinTargetColumn "
             . "WHERE j.$joinLocalColumn = :value";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['value' => $localValue]);
        $rows = $stmt->fetchAll();

        return $this->getEntities($targetEntityClass, $targetFields, $rows);
    }

    /**
     * @param array<string, string> $fields
     * @param array<array<string, mixed>> $rows
     * @throws ReflectionException
     */
    private function getEntities(string $entityClass, array $fields, array $rows): ArrayObject
    {
        $entities = [];
        foreach ($rows as $row) {
            $id = null;
            $idColumn = $fields['id'] ?? null;
            if ($idColumn !== null) {
                $id = (int) $row[$idColumn];
                $identityMapId = $this->buildIdentityMapId($entityClass, $id);
                if ($entity = $this->getFromIdentityMap($identityMapId)) {
                    $entities[] = $entity;
                    continue;
                }
            }

            $entity = $this->getEntity($entityClass, $fields, $row);

            if ($id !== null) {
                $this->setToIdentityMap($this->buildIdentityMapId($entityClass, $id), $entity);
            }

            $entities[] = $entity;
        }

        return new ArrayObject($entities);
    }

    /**
     * @param array<string, string> $fields
     * @param array<string, mixed> $row
     * @throws ReflectionException
     */
    private function getEntity(string $entityClass, array $fields, array $row): object
    {
        $args = array_map(fn ($column) => $row[$column], $fields);
        $entity = new $entityClass(...$args);
        $this->hydrateRelations($entity, $entityClass, $row);
        return $entity;
    }

    private function buildIdentityMapId(string $entityClass, int $id): string
    {
        return "{$entityClass}_{$id}";
    }

    private function getFromIdentityMap(string $identityMapId): ?object
    {
        return $this->identityMap[$identityMapId] ?? null;
    }

    private function setToIdentityMap(string $identityMapId, object $entity): void
    {
        $this->identityMap[$identityMapId] = $entity;
    }
}
