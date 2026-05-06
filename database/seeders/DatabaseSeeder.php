<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Direction;
use App\Models\Enrollment;
use App\Models\Page;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $adminRole = Role::factory()->create([
            'name' => 'Администратор',
            'slug' => 'admin',
        ]);
        $studentRole = Role::factory()->create([
            'name' => 'Студент',
            'slug' => 'student',
        ]);
        $authorRole = Role::factory()->create([
            'name' => 'Автор курса',
            'slug' => 'author',
        ]);

        $directions = Direction::factory()
            ->count(3)
            ->sequence(
                ['name' => 'Бэкенд', 'slug' => 'backend', 'description' => 'Сервер, API, базы данных'],
                ['name' => 'Фронтенд', 'slug' => 'frontend', 'description' => 'Интерфейсы и верстка'],
                ['name' => 'Инструменты', 'slug' => 'tools', 'description' => 'Git, сборка, качество'],
            )
            ->create();

        foreach ($directions as $direction) {
            Course::factory()->count(2)->for($direction)->create();
        }

        $rolePool = [$adminRole->id, $studentRole->id, $authorRole->id];

        $demoUser = User::factory()->create([
            'name' => 'Ерке Баксаисов',
            'email' => 'erke@example.test',
        ]);
        $demoUser->roles()->attach($studentRole->id);
        UserProfile::factory()->for($demoUser)->create([
            'headline' => 'Студент',
            'bio' => 'Учебный профиль.',
        ]);
        Task::factory()->count(4)->for($demoUser)->create();

        $adminUser = User::factory()->create([
            'name' => 'Админ',
            'email' => 'admin@example.test',
        ]);
        $adminUser->roles()->attach($adminRole->id);

        Page::factory()->create([
            'title' => 'Информация',
            'slug' => 'info',
            'body' => "Текст созданный через сид.\n\nДоступен по адресу с slug.",
            'is_published' => true,
        ]);

        $users = User::factory()->count(7)->create();
        foreach ($users as $user) {
            $user->roles()->attach(fake()->randomElement($rolePool));
            UserProfile::factory()->for($user)->create();
            Task::factory()->count(fake()->numberBetween(1, 5))->for($user)->create();

            foreach (Course::inRandomOrder()->limit(fake()->numberBetween(1, 3))->get() as $course) {
                if (! Enrollment::query()->where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
                    Enrollment::factory()->for($user)->for($course)->create();
                }
            }
        }

        foreach (Course::inRandomOrder()->limit(2)->get() as $course) {
            if (! Enrollment::query()->where('user_id', $demoUser->id)->where('course_id', $course->id)->exists()) {
                Enrollment::factory()->for($demoUser)->for($course)->create(['status' => 'in_progress']);
            }
        }
    }
}
