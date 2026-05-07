<?php

namespace Database\Factories;

use App\Models\Construct;
use App\Models\ConstructSnippet;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConstructSnippetFactory extends Factory
{
    protected $model = ConstructSnippet::class;

    public function definition(): array
    {
        return [
            'construct_id' => Construct::factory(),
            'title' => fake()->optional()->sentence(3),
            'code' => "<?php\n\n".fake()->sentence()."\n",
            'sort' => 0,
        ];
    }
}
