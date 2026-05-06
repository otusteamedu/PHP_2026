<?php

namespace App\DataMapper\Mapping;

use App\DataMapper\Mapping\Attribute\Column;
use App\DataMapper\Mapping\Attribute\ManyToMany;
use App\DataMapper\Mapping\Attribute\ManyToOne;
use App\DataMapper\Mapping\Attribute\OneToMany;
use App\DataMapper\Mapping\Attribute\OneToOne;
use App\DataMapper\Mapping\Attribute\Table;
use ReflectionClass;
use ReflectionException;

class MetadataReader
{
    /**
     * @return array<string, string>
     * @throws ReflectionException
     */
    public function getMapping(string $className): array
    {
        $mapping = [];
        $reflection = new ReflectionClass($className);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(Column::class);
            foreach ($attributes as $attribute) {
                $column = $attribute->newInstance();
                $mapping[$property->getName()] = $column->name;
            }
        }

        return $mapping;
    }

    /**
     * @return array<string, array{targetEntity: string, localColumn: string, targetColumn: string}>
     * @throws ReflectionException
     */
    public function getOneToOneRelations(string $className): array
    {
        $relations = [];
        $reflection = new ReflectionClass($className);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(OneToOne::class);
            foreach ($attributes as $attribute) {
                $relation = $attribute->newInstance();
                $relations[$property->getName()] = [
                    'targetEntity' => $relation->targetEntity,
                    'localColumn' => $relation->localColumn,
                    'targetColumn' => $relation->targetColumn,
                ];
            }
        }

        return $relations;
    }

    /**
     * @return array<string, array{targetEntity: string, localColumn: string, targetColumn: string}>
     * @throws ReflectionException
     */
    public function getOneToManyRelations(string $className): array
    {
        $relations = [];
        $reflection = new ReflectionClass($className);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(OneToMany::class);
            foreach ($attributes as $attribute) {
                $relation = $attribute->newInstance();
                $relations[$property->getName()] = [
                    'targetEntity' => $relation->targetEntity,
                    'localColumn' => $relation->localColumn,
                    'targetColumn' => $relation->targetColumn,
                ];
            }
        }

        return $relations;
    }

    /**
     * @return array<string, array{targetEntity: string, localColumn: string, targetColumn: string}>
     * @throws ReflectionException
     */
    public function getManyToOneRelations(string $className): array
    {
        $relations = [];
        $reflection = new ReflectionClass($className);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(ManyToOne::class);
            foreach ($attributes as $attribute) {
                $relation = $attribute->newInstance();
                $relations[$property->getName()] = [
                    'targetEntity' => $relation->targetEntity,
                    'localColumn' => $relation->localColumn,
                    'targetColumn' => $relation->targetColumn,
                ];
            }
        }

        return $relations;
    }

    /**
     * @return array<string, array{targetEntity: string, joinEntity: string, localColumn: string, joinLocalColumn: string, joinTargetColumn: string, targetColumn: string}>
     * @throws ReflectionException
     */
    public function getManyToManyRelations(string $className): array
    {
        $relations = [];
        $reflection = new ReflectionClass($className);

        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(ManyToMany::class);
            foreach ($attributes as $attribute) {
                $relation = $attribute->newInstance();
                $relations[$property->getName()] = [
                    'targetEntity' => $relation->targetEntity,
                    'joinEntity' => $relation->joinEntity,
                    'localColumn' => $relation->localColumn,
                    'joinLocalColumn' => $relation->joinLocalColumn,
                    'joinTargetColumn' => $relation->joinTargetColumn,
                    'targetColumn' => $relation->targetColumn,
                ];
            }
        }

        return $relations;
    }

    /**
     * @throws ReflectionException
     */
    public function getTableName(string $className): string
    {
        $reflection = new ReflectionClass($className);
        $tableNameAttribute = array_first($reflection->getAttributes(Table::class));

        if ($tableNameAttribute) {
            $table = $tableNameAttribute->newInstance();
            return $table->name;
        }

        return strtolower($reflection->getShortName());
    }
}
