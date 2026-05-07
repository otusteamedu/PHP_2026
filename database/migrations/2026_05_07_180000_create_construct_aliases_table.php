<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('construct_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('construct_id')->constrained('constructs')->cascadeOnDelete();
            $table->string('alias', 128);
            $table->timestamps();

            $table->unique(['construct_id', 'alias']);
            $table->index('alias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('construct_aliases');
    }
};

