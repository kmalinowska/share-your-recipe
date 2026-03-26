<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('recipe_id')->constrained('recipes')->cascadeOnDelete();
            // nullable — the comment can be from a guest who does not have an account
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            // only filled in if user_id is null (guest comment)
            $table->string('guest_name')->nullable();

            $table->text('content');

            $table->foreignUuid('parent_id')->nullable()->constrained('comments')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            //indexes
            $table->index('recipe_id');
            $table->index('user_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
