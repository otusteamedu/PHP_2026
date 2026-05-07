<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_query_logs', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id', 64);
            $table->string('username', 64)->nullable();
            $table->string('text', 255);
            $table->string('language', 32)->nullable();
            $table->string('query', 200)->nullable();
            $table->unsignedInteger('results_count')->default(0);
            $table->string('selected_slug', 128)->nullable();
            $table->timestamps();

            $table->index(['chat_id', 'created_at']);
            $table->index(['language', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_query_logs');
    }
};

