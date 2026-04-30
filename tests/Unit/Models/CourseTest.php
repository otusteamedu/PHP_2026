<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use App\Models\Direction;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CourseTest extends TestCase
{
    #[Test]
    public function duration_hours_is_integer_cast(): void
    {
        $course = new Course;
        $course->setRawAttributes(['duration_hours' => '12']);

        $this->assertSame(12, $course->duration_hours);
        $this->assertIsInt($course->duration_hours);
    }

    #[Test]
    public function direction_relation_is_belongsto_direction(): void
    {
        $course = new Course;
        $rel = $course->direction();

        $this->assertInstanceOf(BelongsTo::class, $rel);
        $this->assertInstanceOf(Direction::class, $rel->getRelated());
    }

    #[Test]
    public function enrollments_relation_is_has_many(): void
    {
        $course = new Course;
        $rel = $course->enrollments();

        $this->assertInstanceOf(HasMany::class, $rel);
        $this->assertInstanceOf(Enrollment::class, $rel->getRelated());
    }
}
