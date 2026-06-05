<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\Tag;


class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('recipe');
        // Download user
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'user', 'password' => Hash::make('password')]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'user2@example.com'],
            ['name' => 'user2', 'password' => Hash::make('password')]
        );

        // Download categories by slug
        $breakfast = Category::where('slug', 'breakfast')->first();
        $lunch = Category::where('slug', 'lunch')->first();
        $dinner = Category::where('slug', 'dinner')->first();
        $dessert = Category::where('slug', 'dessert')->first();
        $smoothie = Category::where('slug', 'smoothie')->first();
        $vegetarian = Category::where('slug', 'vegetarian')->first();

        // Recipe definitions
        $recipesData = [
            [
                'user' => $user,
                'category' => $breakfast,
                'title' => 'Oatmeal with Fresh Fruit',
                'preparation' => [
                    'Pour hot milk over the oats and let sit for 5 minutes.',
                    'Add sliced fruits and honey.',
                    'Sprinkle with chia seeds.',
                    'Serve immediately in a bowl.',
                ],
                'preparation_time' => 10,
                'tags' => ['healthy', 'quick', 'vegetarian'],
                'ingredients' => [
                    ['name' => 'oats', 'quantity' => '80', 'unit' => 'g'],
                    ['name' => 'milk', 'quantity' => '200', 'unit' => 'ml'],
                    ['name' => 'banana', 'quantity' => '1', 'unit' => 'pcs'],
                    ['name' => 'strawberries', 'quantity' => '100', 'unit' => 'g'],
                    ['name' => 'honey', 'quantity' => '1', 'unit' => 'tbsp'],
                    ['name' => 'chia seeds', 'quantity' => '1', 'unit' => 'tsp'],
                ],
            ],
            [
                'user' => $user,
                'category' => $lunch,
                'title' => 'Chicken Avocado Salad',
                'preparation' => [
                    'Cook or grill the chicken breast and cut into strips.',
                    'Peel and dice the avocado, drizzle with lemon juice.',
                    'Mix arugula, chicken, avocado, and cherry tomatoes.',
                    'Drizzle with olive oil and season with salt and pepper.',
                ],
                'preparation_time' => 25,
                'tags' => ['healthy', 'high-protein'],
                'ingredients' => [
                    ['name' => 'chicken breast', 'quantity' => '200', 'unit' => 'g'],
                    ['name' => 'avocado', 'quantity' => '1', 'unit' => 'pcs'],
                    ['name' => 'arugula', 'quantity' => '50', 'unit' => 'g'],
                    ['name' => 'cherry tomatoes', 'quantity' => '100', 'unit' => 'g'],
                    ['name' => 'olive oil', 'quantity' => '2', 'unit' => 'tbsp'],
                    ['name' => 'lemon juice', 'quantity' => '1', 'unit' => 'tbsp'],
                ],
            ],
            [
                'user' => $user,
                'category' => $dinner,
                'title' => 'Classic Carbonara',
                'preparation' => [
                    'Cook pasta al dente in salted water.',
                    'Fry pancetta in a dry pan until crispy.',
                    'Mix egg yolks with parmesan and pepper.',
                    'Save some pasta water, then drain the pasta.',
                    'Remove pan from heat, add pasta and pancetta.',
                    'Pour in the egg mixture and stir quickly, adding pasta water for creaminess.',
                ],
                'preparation_time' => 30,
                'tags' => ['italian', 'pasta', 'classic'],
                'ingredients' => [
                    ['name' => 'spaghetti', 'quantity' => '200', 'unit' => 'g'],
                    ['name' => 'pancetta', 'quantity' => '150', 'unit' => 'g'],
                    ['name' => 'egg yolks', 'quantity' => '3', 'unit' => 'pcs'],
                    ['name' => 'parmesan', 'quantity' => '50', 'unit' => 'g'],
                    ['name' => 'black pepper', 'quantity' => '1', 'unit' => 'tsp'],
                ],
            ],
            [
                'user' => $user2,
                'category' => $dessert,
                'title' => 'Chocolate Brownie',
                'preparation' => [
                    'Melt chocolate and butter in a water bath, then let cool.',
                    'Whisk eggs and sugar until fluffy.',
                    'Combine with chocolate, add flour and cocoa, mix well.',
                    'Pour into a lined baking tin.',
                    'Bake for 20 minutes at 180°C — the center should stay moist.',
                ],
                'preparation_time' => 40,
                'tags' => ['chocolate', 'baking', 'sweet'],
                'ingredients' => [
                    ['name' => 'dark chocolate', 'quantity' => '200', 'unit' => 'g'],
                    ['name' => 'butter', 'quantity' => '150', 'unit' => 'g'],
                    ['name' => 'eggs', 'quantity' => '3', 'unit' => 'pcs'],
                    ['name' => 'sugar', 'quantity' => '150', 'unit' => 'g'],
                    ['name' => 'flour', 'quantity' => '80', 'unit' => 'g'],
                    ['name' => 'cocoa powder', 'quantity' => '2', 'unit' => 'tbsp'],
                ],
                'image_path' => 'recipe/chocolate-brownie.jpg',
            ],
            [
                'user' => $user2,
                'category' => $smoothie,
                'title' => 'Green Spinach Smoothie',
                'preparation' => [
                    'Put all ingredients into a blender.',
                    'Blend for 60 seconds on high speed.',
                    'Pour into a glass and serve immediately.',
                ],
                'preparation_time' => 5,
                'tags' => ['vegan', 'healthy', 'quick'],
                'ingredients' => [
                    ['name' => 'fresh spinach', 'quantity' => '60', 'unit' => 'g'],
                    ['name' => 'banana', 'quantity' => '1', 'unit' => 'pcs'],
                    ['name' => 'apple', 'quantity' => '1', 'unit' => 'pcs'],
                    ['name' => 'plant milk', 'quantity' => '250', 'unit' => 'ml'],
                    ['name' => 'fresh ginger', 'quantity' => '1', 'unit' => 'cm'],
                ],
                'image_path' => 'recipe/green-spinach-smoothie.jpg',
            ],
            [
                'user' => $user2,
                'category' => $vegetarian,
                'title' => 'Pumpkin Cream Soup',
                'preparation' => [
                    'Peel pumpkin, dice it, and bake for 25 min at 200°C.',
                    'Sauté onion and garlic in butter until translucent.',
                    'Add baked pumpkin and vegetable broth, simmer for 10 minutes.',
                    'Blend into a cream, season with salt, pepper, and nutmeg.',
                    'Serve with a dollop of cream and pumpkin seeds.',
                ],
                'preparation_time' => 50,
                'tags' => ['soup', 'vegetarian', 'autumn'],
                'ingredients' => [
                    ['name' => 'hokkaido pumpkin', 'quantity' => '800', 'unit' => 'g'],
                    ['name' => 'onion', 'quantity' => '1', 'unit' => 'pcs'],
                    ['name' => 'garlic', 'quantity' => '2', 'unit' => 'cloves'],
                    ['name' => 'vegetable broth', 'quantity' => '500', 'unit' => 'ml'],
                    ['name' => 'butter', 'quantity' => '2', 'unit' => 'tbsp'],
                    ['name' => 'sour cream', 'quantity' => '4', 'unit' => 'tbsp'],
                    ['name' => 'pumpkin seeds', 'quantity' => '2', 'unit' => 'tbsp'],
                    ['name' => 'nutmeg', 'quantity' => '1', 'unit' => 'pinch'],
                ],
                'image_path' => 'recipe/pumpkin-cream-soup.jpg',
            ],
        ];

        // Creating recipes
        $createdRecipes = [];

        foreach ($recipesData as $data) {
            $categorySlug = $data['category']->slug;
            $firstTag = !empty($data['tags']) ? Str::slug($data['tags'][0]) : '';
            $searchQuery = $categorySlug . ($firstTag ? ',' . $firstTag : '');
            // 1. Create or find a recipe
            if (isset($data['image_path'])) {
                $imagePath = $data['image_path'];

                $filename = basename($imagePath);
                $masterPath = database_path('seeders/images/recipe/' . $filename);
                $storageDestinationPath = storage_path('app/public/recipe/' . $filename);

                if (!File::exists($storageDestinationPath) && File::exists($masterPath)) {
                    File::copy($masterPath, $storageDestinationPath);
                }
            } else {
                $imagePath = 'resources/images/placeholders/default-recipe.jpg';
            }

            $recipe = Recipe::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'user_id' => $data['user']->id,
                    'category_id' => $data['category']->id,
                    'title' => $data['title'],
                    'preparation' => $data['preparation'],
                    'preparation_time' => $data['preparation_time'],
                    'image_path' => $imagePath,
                ]
            );

            // 2. Ingredients - firstOrCreate in dictionary, then pivot
            foreach ($data['ingredients'] as $ing) {
                $ingredient = Ingredient::firstOrCreate(['name' => $ing['name']]);

                // Use updateOrCreate on the pivot to avoid duplicates
                DB::table('recipe_ingredients')->updateOrInsert(
                    ['recipe_id' => $recipe->id, 'ingredient_id' => $ingredient->id],
                    ['quantity' => $ing['quantity'], 'unit' => $ing['unit']]
                );
            }

            // 3. Tags - firstOrCreate in dictionary, then sync via Eloquent
            $tagIds = collect($data['tags'])->map(function ($tagName) {
                return Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName)],
                    ['name' => $tagName]
                )->id;
            });
            $recipe->tags()->sync($tagIds);
            $createdRecipes[] = $recipe;
        }

        // Favorites - Some examples
        $this->seedFavourites($user, $user2, $createdRecipes);
        // Comments with answers
        $this->seedComments($user, $user2, $createdRecipes);
    }

        private function seedFavourites($user, $user2, $recipes): void
        {
            // user likes recipes user2
            DB::table('favourites')->insertOrIgnore([
                ['id' => Str::uuid(), 'user_id' => $user->id, 'recipe_id' => $recipes[2]->id, 'created_at' => now()], // carbonara
                ['id' => Str::uuid(), 'user_id' => $user->id, 'recipe_id' => $recipes[3]->id, 'created_at' => now()], // brownie
            ]);
        }

        private function seedComments($user, $user2, $recipes): void
        {
            // comment user2 to recipe user + answer user
            $comment1Id = Str::uuid()->toString();
            DB::table('comments')->insertOrIgnore([[
                'id' => $comment1Id,
                'recipe_id' => $recipes[0]->id,
                'user_id' => $user2->id,
                'guest_name' => null,
                'content' => 'Amazing oatmeal! I added a tablespoon of peanut butter and highly recommend it!',
                'parent_id' => null,
                'created_at' => now()->subDays(3),
            ]]);

            DB::table('comments')->insertOrIgnore([[
                'id' => Str::uuid(),
                'recipe_id' => $recipes[0]->id,
                'user_id' => $user->id,
                'guest_name' => null,
                'content' => 'Great idea with the peanut butter, I have to try that myself!',
                'parent_id' => $comment1Id,
                'created_at' => now()->subDays(2),
            ]]);

            // quest comment
            DB::table('comments')->insertOrIgnore([[
                'id' => Str::uuid(),
                'recipe_id' => $recipes[2]->id,
                'user_id' => null,
                'guest_name' => 'Tom',
                'content' => 'Made this for dinner yesterday, the family loved it. Definitely worth recommending!',
                'parent_id' => null,
                'created_at' => now()->subDays(1),
            ]]);
        }
}
