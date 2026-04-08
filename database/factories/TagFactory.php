<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Tag::class;
    public function definition(): array
    {
        $tags = [
            'Breakfast','Brunch','Lunch','Dinner','Snack','Dessert','Appetizer','Side Dish','Main Course','Street Food',
            'Italian','Mexican','Asian','Indian','Mediterranean','American','French','Thai','Japanese','Middle Eastern',
            'Vegetarian','Vegan','Gluten Free','Dairy Free','Keto','Low Carb','High Protein','Paleo','Healthy','Sugar Free',
            'Quick','Easy','One Pot','No Bake','Slow Cooked','30 Minutes','Meal Prep',
            'Baked','Grilled','Fried','Roasted','Steamed','Air Fryer',
            'Spicy','Sweet','Savory','Tangy','Creamy','Crunchy','Comfort Food'
        ];

        return [
            // use randomElement from tags list
            'name' => fake()->unique()->randomElement($tags) ?? fake()->unique()->words(2,true),
        ];
    }
}
