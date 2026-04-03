<?php

class UserDocument
{
    public function __construct(
        public ?string $id = null,
        public string $username = '',
        public ?string $createdAt = null
    ) {
    }
}