<?php

namespace Database\Factories;

use App\Models\Construct;
use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ConstructFactory extends Factory
{
    protected $model = Construct::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'language_id' => Language::factory(),
            'slug' => Str::slug($title),
            'title' => $title,
            'summary' => fake()->optional()->sentence(),
            'details' => fake()->optional()->paragraphs(2, true),
        ];
    }
}
