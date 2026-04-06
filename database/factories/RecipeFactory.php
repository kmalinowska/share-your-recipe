<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Recipe::class;

    public function definition(): array
    {
        $title = fake()->unique()->randomElement([
            'Classic Spaghetti Bolognese', 'Creamy Tomato Soup', 'Fluffy Pancakes',
            'Greek Salad', 'Chicken Curry', 'Potato Pancakes',
            'Vegetable Fried Rice', 'Avocado Toast', 'Cheese Omelette',
            'Vegetable Soup', 'Pesto Pasta', 'Egg Toast',
            'Banana Smoothie', 'Chocolate Brownie', 'Caesar Salad',
            'Grilled Salmon', 'Beef Tacos', 'Mushroom Risotto',
        ]);

        return [
            'user_id'          => User::factory(),
            // Requires $this->seed(CategorySeeder::class) in test beforeEach
            'category_id'      => fn() => Category::inRandomOrder()->first()?->id,
            'title'            => $title,
            'preparation'      => fake()->paragraphs(2, true),
            'preparation_time' => fake()->randomElement([5, 10, 15, 20, 30, 45, 60, 90]),
            'image_path'       => null,
        ];
    }

    // State: recipe with an image
    public function withImage(): static
    {
        return $this->state(['image_path' => 'recipes/test-image.jpg']);
    }

    // State: recipe for a specific user
    public function forUser(User $user): static
    {
        return $this->state(['user_id' => $user->id]);
    }

    // State: recipe for a specific category
    public function forCategory(Category $category): static
    {
        return $this->state(['category_id' => $category->id]);
    }
}
