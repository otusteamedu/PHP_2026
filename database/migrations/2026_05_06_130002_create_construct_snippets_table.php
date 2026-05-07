<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('construct_snippets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('construct_id')->constrained('constructs')->cascadeOnDelete();
            $table->string('title', 255)->nullable();
            $table->longText('code');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['construct_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('construct_snippets');
    }
};

