<?php

class User
{
    public function __construct(
        public ?int $id,
        public string $username,
        public ?string $createdAt = null
    ) {
        $this->createdAt = date('Y-m-d H:i:s');
    }
}