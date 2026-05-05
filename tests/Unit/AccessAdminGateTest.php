<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Gate;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class AccessAdminGateTest extends UnitTestCase
{
    #[Test]
    public function access_admin_allows_when_admin_role_exists(): void
    {
        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('where')->with('slug', 'admin')->andReturn($relation);
        $relation->shouldReceive('exists')->andReturn(true);

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('roles')->withNoArgs()->andReturn($relation);

        $this->assertTrue(Gate::forUser($user)->allows('access-admin'));
    }

    #[Test]
    public function access_admin_denies_when_admin_role_missing(): void
    {
        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('where')->with('slug', 'admin')->andReturn($relation);
        $relation->shouldReceive('exists')->andReturn(false);

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('roles')->withNoArgs()->andReturn($relation);

        $this->assertFalse(Gate::forUser($user)->allows('access-admin'));
    }
}
