<?php

namespace Tests\Unit\Models;

use App\Models\Enrollment;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    #[Test]
    public function implements_password_reset_contract(): void
    {
        $this->assertInstanceOf(CanResetPassword::class, new User);
    }

    #[Test]
    public function password_is_hashed_when_assigned(): void
    {
        $user = new User;
        $user->password = 'plain-secret';

        $this->assertTrue(Hash::check('plain-secret', $user->password));
    }

    #[Test]
    public function sensitive_attributes_are_hidden_in_array_form(): void
    {
        $user = new User;
        $user->forceFill([
            'name' => 'T',
            'email' => 't@t.test',
            'password' => 'x',
            'remember_token' => 'tok',
        ]);

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    #[Test]
    public function relation_methods_return_expected_types(): void
    {
        $user = new User;

        $this->assertInstanceOf(BelongsToMany::class, $user->roles());
        $this->assertInstanceOf(Role::class, $user->roles()->getRelated());

        $this->assertInstanceOf(HasOne::class, $user->profile());
        $this->assertInstanceOf(UserProfile::class, $user->profile()->getRelated());

        $this->assertInstanceOf(HasMany::class, $user->tasks());
        $this->assertInstanceOf(Task::class, $user->tasks()->getRelated());

        $this->assertInstanceOf(HasMany::class, $user->enrollments());
        $this->assertInstanceOf(Enrollment::class, $user->enrollments()->getRelated());
    }
}
