<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ingredients = [
            'chicken breast','ground beef','pork loin','salmon','tuna','eggs','tofu','chickpeas','lentils','black beans',
            'onion','garlic','carrot','potato','sweet potato','broccoli','spinach','zucchini','bell pepper','tomato',
            'apple','banana','lemon','lime','orange','strawberries','blueberries','avocado',
            'milk','butter','cheese','parmesan','cream','yogurt',
            'rice','pasta','flour','oats','bread','quinoa',
            'salt','black pepper','paprika','chili flakes','oregano','basil','cumin','turmeric','cinnamon','olive oil'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($ingredients)
                ?? $this->faker->unique()->word(),
        ];
    }
}
