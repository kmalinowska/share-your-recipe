<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: add a temporary column for JSON
        Schema::table('recipes', function (Blueprint $table) {
            $table->json('preparation_json')->nullable()->after('preparation');
        });

        // Step 2: migrate existing string data to JSON array
        // Split by newline — each line becomes a separate step
        DB::table('recipes')->orderBy('id')->each(function ($recipe) {
            if ($recipe->preparation) {
                $steps = collect(explode("\n", $recipe->preparation))
                    ->map(fn($s) => trim($s))
                    ->filter(fn($s) => $s !== '')
                    ->values()
                    ->toArray();

                DB::table('recipes')
                    ->where('id', $recipe->id)
                    ->update(['preparation_json' => json_encode($steps)]);
            }
        });

        // Step 3: drop old column and rename new one
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('preparation');
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->renameColumn('preparation_json', 'preparation');
        });
    }

    public function down(): void
    {
        // Reverse: convert JSON back to newline-separated string
        Schema::table('recipes', function (Blueprint $table) {
            $table->text('preparation_text')->nullable()->after('preparation');
        });

        DB::table('recipes')->orderBy('id')->each(function ($recipe) {
            if ($recipe->preparation) {
                $steps = json_decode($recipe->preparation, true) ?? [];
                $text  = implode("\n", $steps);

                DB::table('recipes')
                    ->where('id', $recipe->id)
                    ->update(['preparation_text' => $text]);
            }
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('preparation');
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->renameColumn('preparation_text', 'preparation');
        });
    }
};
