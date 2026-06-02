<?php

declare (strict_types = 1);

interface BuilderInterface
{
    public function select(array $fields): self;

    public function from(string $table): self;

    public function where(string $condition): self;

    public function andWhere(string $condition): self;

    public function join(string $table, string $on): self;

    public function orderBy(string $field, string $direction = 'ASC'): self;

    public function limit(int $limit): self;

    public function build(): string;

    public function reset(): self;
}
