<?php

namespace App\DataMapper;

use App\DataMapper\Mapping\Column;
use App\DataMapper\Mapping\Table;

#[Table(name: 'users')]
final class User
{
    public function __construct(
        #[Column(name: 'id')]
        public ?int $id,
        #[Column(name: 'name')]
        public string $name,
        #[Column(name: 'email')]
        public string $email,
    ) {
    }
}
