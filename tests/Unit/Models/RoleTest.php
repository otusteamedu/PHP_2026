<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleTest extends TestCase
{
    #[Test]
    public function users_relation_is_belongs_to_many(): void
    {
        $role = new Role;
        $rel = $role->users();

        $this->assertInstanceOf(BelongsToMany::class, $rel);
        $this->assertInstanceOf(User::class, $rel->getRelated());
    }
}
