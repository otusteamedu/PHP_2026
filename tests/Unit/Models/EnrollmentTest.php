<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    #[Test]
    public function enrolled_at_is_datetime_cast(): void
    {
        $enrollment = new Enrollment;
        $enrollment->setRawAttributes([
            'enrolled_at' => '2026-04-29 08:15:00',
        ]);

        $this->assertSame('2026-04-29 08:15:00', $enrollment->enrolled_at->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function user_and_course_relations(): void
    {
        $enrollment = new Enrollment;
        $userRel = $enrollment->user();
        $courseRel = $enrollment->course();

        $this->assertInstanceOf(BelongsTo::class, $userRel);
        $this->assertInstanceOf(User::class, $userRel->getRelated());

        $this->assertInstanceOf(BelongsTo::class, $courseRel);
        $this->assertInstanceOf(Course::class, $courseRel->getRelated());
    }
}
