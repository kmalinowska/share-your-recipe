<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Breakfast','Brunch','Lunch','Dinner','Snack','Dessert','Appetizer','Side Dish','Main Course','Street Food',
            'Italian','Mexican','Asian','Indian','Mediterranean','American','French','Thai','Japanese','Middle Eastern',
            'Vegetarian','Vegan','Gluten Free','Dairy Free','Keto','Low Carb','High Protein','Paleo','Healthy','Sugar Free',
            'Quick','Easy','One Pot','No Bake','Slow Cooked','30 Minutes','Meal Prep',
            'Baked','Grilled','Fried','Roasted','Steamed','Air Fryer',
            'Spicy','Sweet','Savory','Tangy','Creamy','Crunchy','Comfort Food'
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tag)],
                ['name' => $tag]
            );
        }
    }
}
