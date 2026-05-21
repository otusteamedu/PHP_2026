<?php

namespace App\AbstractFactoryAndBuilder\DB\PostgreSQL;

use App\AbstractFactoryAndBuilder\Contracts\QueryBuilderInterface;

class PostgreSQLQueryBuilder implements QueryBuilderInterface
{
    private array $selectFields = ['*'];
    private string $table = '';
    private array $whereConditions = [];
    private array $joinConditions = [];
    private array $orderByConditions = [];
    private ?int $limit = null;
    private ?int $offset = null;

    public function select(array $columns): self
    {
        $this->selectFields = $columns;
        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        $this->whereConditions[] = "{$column} {$operator} {$value}";
        return $this;
    }

    public function join(string $table, ?string $localKey = null, ?string $foreignKey = null): self
    {
        $this->joinConditions[] = [
            'table' => $table,
            'localKey' => $localKey,
            'foreignKey' => $foreignKey,
        ];
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function build(): string
    {
        if (!$this->table) {
            throw new \InvalidArgumentException('Table is not set');
        }

        $sql = 'SELECT ' . implode(', ', $this->selectFields) . ' FROM ' . $this->table;

        if (!empty($this->joinConditions)) {
            foreach ($this->joinConditions as $join) {
                $sql .= ' JOIN ' . $join['table'] . ' ON ' . $this->table . '.' . ($join['localKey'] ?? 'id') . ' = ' . $join['table'] . '.' . ($join['foreignKey'] ?? ($join['table'] . '.id'));
            }
        }

        if (!empty($this->whereConditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->whereConditions);
        }

        if (!empty($this->orderByConditions)) {
            $sql .= ' ORDER BY ' . implode(' ORDER BY ', $this->orderByConditions);
        }

        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }
}
