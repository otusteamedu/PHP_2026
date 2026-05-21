<?php

namespace App\Template\Entitys;

class User
{
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $email
    ) {}
}