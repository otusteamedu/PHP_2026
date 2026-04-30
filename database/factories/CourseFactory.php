<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Direction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(fake()->numberBetween(2, 4));

        return [
            'direction_id' => Direction::factory(),
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('####'),
            'summary' => fake()->optional(0.9)->paragraph(),
            'duration_hours' => fake()->optional(0.8)->numberBetween(8, 120),
        ];
    }
}
