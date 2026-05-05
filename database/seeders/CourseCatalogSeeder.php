<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseCatalogSeeder extends Seeder
{
    /**
     * Базовые направления и курсы (бывшие insert-ы из миграций).
     */
    public function run(): void
    {
        $now = now();

        DB::table('directions')->insert([
            [
                'name' => 'Бэкенд',
                'slug' => 'backend',
                'description' => 'Серверная разработка, API, БД',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Фронтенд',
                'slug' => 'frontend',
                'description' => 'Верстка, интерфейсы, адаптив',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Инструменты',
                'slug' => 'tools',
                'description' => 'Окружение, сборка, качество кода',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $backendId = DB::table('directions')->where('slug', 'backend')->value('id');
        $frontendId = DB::table('directions')->where('slug', 'frontend')->value('id');
        $toolsId = DB::table('directions')->where('slug', 'tools')->value('id');

        DB::table('courses')->insert([
            [
                'direction_id' => $backendId,
                'title' => 'Laravel: основы',
                'slug' => 'laravel-intro',
                'summary' => 'Маршруты, Blade, структура приложения',
                'duration_hours' => 40,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'direction_id' => $backendId,
                'title' => 'Базы данных и SQL',
                'slug' => 'sql-databases',
                'summary' => 'Проектирование схем, запросы, индексы',
                'duration_hours' => 32,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'direction_id' => $frontendId,
                'title' => 'Интерфейсы на Bootstrap',
                'slug' => 'bootstrap-ui',
                'summary' => 'Сетка, компоненты, адаптивная верстка',
                'duration_hours' => 24,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'direction_id' => $toolsId,
                'title' => 'Git и командная работа',
                'slug' => 'git-workflow',
                'summary' => 'Ветки, Pull Request, разрешение конфликтов',
                'duration_hours' => 16,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
