<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use App\Models\Direction;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DirectionTest extends TestCase
{
    #[Test]
    public function courses_relation_is_has_many(): void
    {
        $direction = new Direction;
        $rel = $direction->courses();

        $this->assertInstanceOf(HasMany::class, $rel);
        $this->assertInstanceOf(Course::class, $rel->getRelated());
    }
}
