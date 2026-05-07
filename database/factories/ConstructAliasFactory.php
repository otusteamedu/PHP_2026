<?php

namespace Database\Factories;

use App\Models\Construct;
use App\Models\ConstructAlias;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConstructAliasFactory extends Factory
{
    protected $model = ConstructAlias::class;

    public function definition(): array
    {
        return [
            'construct_id' => Construct::factory(),
            'alias' => fake()->unique()->words(2, true),
        ];
    }
}
