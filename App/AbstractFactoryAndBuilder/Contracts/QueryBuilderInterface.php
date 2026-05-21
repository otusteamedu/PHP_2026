<?php

namespace App\AbstractFactoryAndBuilder\Contracts;

interface QueryBuilderInterface
{
    public function select(array $columns): self;
    public function from(string $table): self;
    public function where(string $column, string $operator, mixed $value): self;
    public function join(string $table, ?string $localKey = null, ?string $foreignKey = null): self;
    public function limit(int $limit): self;
    public function offset(int $offset): self;
    public function build(): string;
}