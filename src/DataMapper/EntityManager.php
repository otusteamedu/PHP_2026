<?php

namespace App\DataMapper;

use App\DataMapper\Mapping\MetadataReader;
use ArrayObject;
use InvalidArgumentException;
use PDO;
use ReflectionClass;
use ReflectionException;

class EntityManager
{
    private const int DEFAULT_LIMIT = 100;

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
        $table = $this->validateAndQuoteIdentifier($this->metadataReader->getTableName($entity::class));
        $fields = $this->metadataReader->getMapping($entity::class);

        $data = [];
        foreach ($fields as $prop => $column) {
            $data[$column] = $entity->{$prop};
        }

        if ($entity->id === null) {
            unset($data['id']);
            $columns = $placeholders = [];
            foreach (array_keys($data) as $column) {
                $columns[] = $this->validateAndQuoteIdentifier($column);
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
                    |> (fn($columns) => array_map(fn($c) => $this->validateAndQuoteIdentifier($c) . " = :$c", $columns))
                    |> (fn($bindColumns) => implode(', ', $bindColumns));

            $idColumn = $this->validateAndQuoteIdentifier($fields['id'] ?? 'id');
            $sql = "UPDATE {$table} SET {$set} WHERE {$idColumn} = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function find(int $id, string $entityClass): ?object
    {
        $table = $this->validateAndQuoteIdentifier($this->metadataReader->getTableName($entityClass));
        $fields = $this->metadataReader->getMapping($entityClass);

        $identityMapId = $this->buildIdentityMapId($entityClass, $id);
        if ($entity = $this->getFromIdentityMap($identityMapId)) {
            return $entity;
        }

        $selectColumns = $this->buildSelectColumns($fields);
        $idColumn = $this->validateAndQuoteIdentifier($fields['id'] ?? 'id');
        $sql = "SELECT {$selectColumns} FROM {$table} WHERE {$idColumn} = :id LIMIT 1";
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
    public function all(
        string $entityClass,
        array $conditions = [],
        int $limit = self::DEFAULT_LIMIT,
        int $offset = 0
    ): ArrayObject {
        $table = $this->validateAndQuoteIdentifier($this->metadataReader->getTableName($entityClass));
        $fields = $this->metadataReader->getMapping($entityClass);

        $selectColumns = $this->buildSelectColumns($fields);
        $sql = "SELECT {$selectColumns} FROM {$table}";

        $safeConditions = array_intersect_key($conditions, $fields);
        $bindings = [];
        foreach ($safeConditions as $property => $value) {
            $column = $fields[$property];
            $bindings[] = $this->validateAndQuoteIdentifier($column) . " = :{$property}";
        }

        if (!empty($bindings)) {
            $sql .= ' WHERE ' . implode(' AND ', $bindings);
        }
        $sql .= ' LIMIT :limit OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);
        foreach ($safeConditions as $property => $value) {
            $stmt->bindValue(":{$property}", $value);
        }
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, $offset), PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return $this->getEntities($entityClass, $fields, $rows);
    }

    /**
     * @throws ReflectionException
     */
    private function findOneByColumn(string $entityClass, string $column, mixed $value): ?object
    {
        $table = $this->validateAndQuoteIdentifier($this->metadataReader->getTableName($entityClass));
        $fields = $this->metadataReader->getMapping($entityClass);
        if (!in_array($column, $fields)) {
            throw new InvalidArgumentException("Column '{$column}' is not mapped for {$entityClass}.");
        }

        $selectColumns = $this->buildSelectColumns($fields);
        $column = $this->validateAndQuoteIdentifier($column);
        $sql = "SELECT {$selectColumns} FROM {$table} WHERE {$column} = :value LIMIT 1";
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
    private function findOneToManyData(string $entityClass, string $column, mixed $value): ArrayObject
    {
        $table = $this->validateAndQuoteIdentifier($this->metadataReader->getTableName($entityClass));
        $fields = $this->metadataReader->getMapping($entityClass);
        if (!in_array($column, $fields)) {
            throw new InvalidArgumentException("Column '{$column}' is not mapped for {$entityClass}.");
        }

        $selectColumns = $this->buildSelectColumns($fields);
        $column = $this->validateAndQuoteIdentifier($column);
        $stmt = $this->pdo->prepare("SELECT {$selectColumns} FROM {$table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);

        return $this->getEntities($entityClass, $fields, $stmt->fetchAll());
    }

    /**
     * @throws ReflectionException
     */
    private function findManyToManyData(
        string $targetClass,
        string $joinClass,
        string $targetCol,
        string $joinLocal,
        string $joinTarget,
        mixed $localValue
    ): ArrayObject {
        $targetTable = $this->validateAndQuoteIdentifier($this->metadataReader->getTableName($targetClass));
        $targetFields = $this->metadataReader->getMapping($targetClass);
        $joinTable = $this->validateAndQuoteIdentifier($this->metadataReader->getTableName($joinClass));
        $joinFields = $this->metadataReader->getMapping($joinClass);

        if (!in_array($targetCol, $targetFields, true)) {
            throw new InvalidArgumentException("Column '{$targetCol}' is not mapped for {$targetClass}.");
        }
        if (!in_array($joinLocal, $joinFields, true) || !in_array($joinTarget, $joinFields, true)) {
            throw new InvalidArgumentException("Join columns are not mapped for {$joinClass}.");
        }

        $targetSelectColumns = $this->buildSelectColumns($targetFields, 't');
        $targetCol = $this->validateAndQuoteIdentifier($targetCol);
        $joinLocal = $this->validateAndQuoteIdentifier($joinLocal);
        $joinTarget = $this->validateAndQuoteIdentifier($joinTarget);

        $sql = "SELECT {$targetSelectColumns} FROM {$targetTable} t "
            . "INNER JOIN {$joinTable} j ON t.{$targetCol} = j.{$joinTarget} "
            . "WHERE j.{$joinLocal} = :value";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['value' => $localValue]);

        return $this->getEntities($targetClass, $targetFields, $stmt->fetchAll());
    }

    /**
     * @param array<string, mixed> $row
     * @throws ReflectionException
     */
    private function hydrateRelations(object $entity, string $entityClass, array $row): void
    {
        $relations = $this->metadataReader->getOneToOneRelations($entityClass);
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

        $toManyRelations = $this->metadataReader->getOneToManyRelations($entityClass);
        foreach ($toManyRelations as $property => $relation) {
            $localValue = $row[$relation['localColumn']] ?? null;
            if ($localValue === null) {
                $entity->{$property} = [];
                continue;
            }

            $entity->{$property} = $this->createOneToManyRelation(
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

            $entity->{$property} = $this->createManyToManyRelation(
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
    private function createOneToManyRelation(string $entityClass, string $column, mixed $value): ArrayObject
    {
        $initializer = fn (): ArrayObject => $this->findOneToManyData($entityClass, $column, $value);

        return new LazyEntityCollection($initializer);
    }

    /**
     * @throws ReflectionException
     */
    private function createManyToManyRelation(
        string $targetEntityClass,
        string $joinEntityClass,
        string $targetColumn,
        string $joinLocalColumn,
        string $joinTargetColumn,
        mixed $localValue
    ): ArrayObject {
        $initializer = fn (): ArrayObject => $this->findManyToManyData(
            $targetEntityClass,
            $joinEntityClass,
            $targetColumn,
            $joinLocalColumn,
            $joinTargetColumn,
            $localValue
        );

        return new LazyEntityCollection($initializer);
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

    /**
     * @param array<string, string> $fields
     */
    private function buildSelectColumns(array $fields, ?string $alias = null): string
    {
        $quotedFields = array_map(function (string $column) use ($alias): string {
            $quotedColumn = $this->validateAndQuoteIdentifier($column);
            if ($alias === null) {
                return $quotedColumn;
            }

            return $alias . '.' . $quotedColumn;
        }, $fields);

        return implode(', ', $quotedFields);
    }

    private function validateAndQuoteIdentifier(string $identifier): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException("Unsafe SQL identifier: {$identifier}");
        }

        return '"' . $identifier . '"';
    }
}
