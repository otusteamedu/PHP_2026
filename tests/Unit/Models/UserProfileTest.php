<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    #[Test]
    public function user_relation_is_belongsto_user(): void
    {
        $profile = new UserProfile;
        $rel = $profile->user();

        $this->assertInstanceOf(BelongsTo::class, $rel);
        $this->assertInstanceOf(User::class, $rel->getRelated());
    }
}
