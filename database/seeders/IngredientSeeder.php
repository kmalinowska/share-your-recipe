<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            'chicken breast','ground beef','pork loin','salmon','tuna','eggs','tofu','chickpeas','lentils','black beans',
            'onion','garlic','carrot','potato','sweet potato','broccoli','spinach','zucchini','bell pepper','tomato',
            'apple','banana','lemon','lime','orange','strawberries','blueberries','avocado',
            'milk','butter','cheese','parmesan','cream','yogurt',
            'rice','pasta','flour','oats','bread','quinoa',
            'salt','black pepper','paprika','chili flakes','oregano','basil','cumin','turmeric','cinnamon','olive oil'
        ];

        foreach ($ingredients as $name) {
            Ingredient::firstOrCreate(
                ['name' => $name],
            );
        }
    }
}
