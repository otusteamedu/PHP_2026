<?php

namespace App\AbstractFactoryAndBuilder\Contracts;
use PDO;

interface ConnectionInterface
{
    public function getConnection(): PDO;
}
