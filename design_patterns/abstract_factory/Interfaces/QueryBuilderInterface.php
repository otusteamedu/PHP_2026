<?php

declare (strict_types = 1);

interface QueryBuilderInterface
{
    public function select(string $table): self;

    public function limit(int $limit): self;

    public function get(): string;

    public function paginate(int $limit, int $offset): self;
}