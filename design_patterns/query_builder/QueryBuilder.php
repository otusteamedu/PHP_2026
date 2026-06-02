<?php

declare (strict_types = 1);

class QueryBuilder implements BuilderInterface
{
    protected array $fields   = [];
    protected string $table   = '';
    protected array $where    = [];
    protected array $joins    = [];
    protected string $orderBy = '';
    protected string $limit   = '';

    public function select(array $fields = ['*']): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function join(string $table, string $on): self
    {
        $this->joins[] = "JOIN {$table} ON {$on}";

        return $this;
    }

    public function where(string $condition): self
    {
        if (! empty($this->where)) {
            throw new LogicException('where() уже был вызван. Используйте andWhere()');
        }

        $this->where[] = $condition;

        return $this;
    }

    public function andWhere(string $condition): self
    {
        $this->where[] = 'AND ' . $condition;

        return $this;
    }

    public function orderBy(string $field, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction);

        if (! in_array($direction, ['ASC', 'DESC'])) {
            throw new InvalidArgumentException('Ошибка в выборе инструкции для сортировки');
        }

        $this->orderBy = "ORDER BY {$field} {$direction}";

        return $this;
    }

    public function limit(int $limit, ?int $offset = null): self
    {
        if ($offset !== null) {
            $this->limit = "LIMIT {$offset}, {$limit}";
        } else {
            $this->limit = "LIMIT {$limit}";
        }
        return $this;
    }

    public function build(): string
    {
        if (empty($this->fields)) {
            throw new \Exception("Ошибка сборки запроса: не указаны поля (SELECT).");
        }
        if (empty($this->table)) {
            throw new \Exception("Ошибка сборки запроса: не указана таблица (FROM).");
        }

        $sql = "SELECT " . implode(', ', $this->fields) . " FROM " . $this->table;

        if (! empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }

        if (! empty($this->where)) {
            $sql .= ' WHERE ' . implode(' ', $this->where);
        }

        if (! empty($this->orderBy)) {
            $sql .= " " . $this->orderBy;
        }

        if (! empty($this->limit)) {
            $sql .= " " . $this->limit;
        }

        $result = $sql;

        $this->reset();

        return $result;
    }

    public function reset(): self
    {
        $this->fields  = [];
        $this->table   = '';
        $this->where   = [];
        $this->joins   = [];
        $this->orderBy = '';
        $this->limit   = '';

        return $this;
    }
}
