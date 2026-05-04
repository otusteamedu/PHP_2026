<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'status' => fake()->randomElement(['planned', 'in_progress', 'completed']),
            'comment' => fake()->optional(0.3)->sentence(),
            'enrolled_at' => fake()->optional(0.9)->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
