<?php

class User
{
    public function __construct(
        public ?int $id = null,

        public string $username = '',

        /** attr[table_name: created_at] */
        public ?string $createdAt = null,
    ) {
        $this->createdAt = date('Y-m-d H:i:s');
    }
}