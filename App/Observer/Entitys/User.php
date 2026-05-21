<?php

namespace App\Observer\Entitys;

class User
{
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $email
    ) {}

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
