<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direction_id')
                ->constrained('directions')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->unsignedSmallInteger('duration_hours')->nullable();
            $table->timestamps();

            $table->index('direction_id');
        });

        $backendId = DB::table('directions')->where('slug', 'backend')->value('id');
        $frontendId = DB::table('directions')->where('slug', 'frontend')->value('id');
        $toolsId = DB::table('directions')->where('slug', 'tools')->value('id');

        $now = now();

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

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('courses');
        Schema::enableForeignKeyConstraints();
    }
};
