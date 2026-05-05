<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('###'),
            'body' => fake()->optional(0.9)->paragraphs(3, true),
            'is_published' => true,
        ];
    }
}
