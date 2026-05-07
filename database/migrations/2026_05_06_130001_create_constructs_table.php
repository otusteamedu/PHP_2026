<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('constructs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('slug', 128);
            $table->string('title', 255);
            $table->text('summary')->nullable();
            $table->longText('details')->nullable();
            $table->timestamps();

            $table->unique(['language_id', 'slug']);
            $table->index(['language_id', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('constructs');
    }
};

