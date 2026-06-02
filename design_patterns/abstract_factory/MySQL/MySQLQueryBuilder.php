<?php

declare (strict_types = 1);
class MySQLQueryBuilder implements QueryBuilderInterface
{
    private string $table = '';
    private string $limit = '';

    public function select(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = "LIMIT {$limit}";

        return $this;
    }

    public function paginate(int $limit, int $offset): self
    {
        $this->limit = "LIMIT {$offset}, {$limit}";

        return $this;
    }

    public function get(): string
    {
        $sql         = "SELECT * FROM {$this->table} {$this->limit}";
        
        $this->table = '';
        $this->limit = '';

        return $sql;
    }
}
