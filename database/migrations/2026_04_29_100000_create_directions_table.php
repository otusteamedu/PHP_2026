<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('directions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

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
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('directions');
        Schema::enableForeignKeyConstraints();
    }
};
