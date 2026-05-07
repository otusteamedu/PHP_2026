<?php

namespace Database\Factories;

use App\Models\Construct;
use App\Models\ConstructLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConstructLinkFactory extends Factory
{
    protected $model = ConstructLink::class;

    public function definition(): array
    {
        return [
            'construct_id' => Construct::factory(),
            'title' => fake()->optional()->sentence(3),
            'url' => fake()->url(),
            'sort' => 0,
        ];
    }
}
