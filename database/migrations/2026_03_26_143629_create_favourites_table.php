<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favourites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('recipe_id')->constrained('recipes')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            // User can add a recipe to favorites only once
            $table->unique(['user_id', 'recipe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favourites');
    }
};
