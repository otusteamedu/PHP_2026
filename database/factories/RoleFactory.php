<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $label = fake()->unique()->randomElement([
            'Редактор',
            'Модератор',
            'Куратор',
            'Наблюдатель',
            'Ментор',
            'Гость',
        ]).'-'.fake()->unique()->numerify('##');

        return [
            'name' => $label,
            'slug' => Str::slug($label).'-'.fake()->unique()->numberBetween(100, 99999),
        ];
    }
}
