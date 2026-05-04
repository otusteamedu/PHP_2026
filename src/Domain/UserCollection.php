<?php

declare(strict_types=1);

namespace App\Domain;

use App\DataMapper\EntityCollection;

/**
 * @extends EntityCollection<User>
 */
class UserCollection extends EntityCollection
{
    public function __construct(array $users = [])
    {
        parent::__construct($users, User::class);
    }
}
