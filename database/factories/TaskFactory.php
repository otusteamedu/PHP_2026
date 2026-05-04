<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => rtrim(fake()->sentence(fake()->numberBetween(3, 6)), '.'),
            'description' => fake()->optional(0.65)->paragraph(),
            'is_done' => fake()->boolean(35),
            'due_at' => fake()->optional(0.5)->dateTimeBetween('now', '+3 weeks'),
        ];
    }
}
