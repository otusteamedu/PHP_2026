<?php

namespace App\DataMapper\Mapping;

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
