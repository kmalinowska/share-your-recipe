<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Recipe;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed default categories required by recipes and filtering logic
    $this->seed(CategorySeeder::class);
});

// ==========================================
// 1. HTTP STATUS & VIEW CHECKS
// ==========================================

// Verifies that the home page loads successfully for guests and renders the correct view
it('loads the home page successfully and renders the correct view', function () {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertViewIs('home');
    $response->assertViewHasAll(['categories', 'latestRecipes', 'recentComments']);
});

// Verifies that the home page works correctly even when there are no recipes or comments
it('loads home page correctly when database has no recipes or comments', function () {

    Comment::query()->delete();
    Recipe::query()->delete();

    $response = $this->get(route('home'));

    $response->assertStatus(200);

    expect($response->viewData('latestRecipes'))->toHaveCount(0)
        ->and($response->viewData('recentComments'))->toHaveCount(0);
});

// ==========================================
// 2. CATEGORIES LOGIC
// ==========================================

// Verifies that all categories are passed to the view ordered alphabetically by name
it('passes all categories to the view ordered alphabetically', function () {
    // download categories from the seeder and rename them for the sorting test
    $categories = Category::take(3)->get();
    $categories[0]->update(['name' => 'Cereal']);
    $categories[1]->update(['name' => 'Breakfast']);
    $categories[2]->update(['name' => 'Appetizer']);

    $response = $this->get(route('home'));

    $categoriesInView = $response->viewData('categories');

    // check if the first one in the view is the one with the letter 'A' and the second one with the letter 'B'
    expect($categoriesInView->first()->name)->toBe('Appetizer')
        ->and($categoriesInView->skip(1)->first()->name)->toBe('Breakfast');
});

// ==========================================
// 3. RECIPES LOGIC
// ==========================================

// Verifies that only the 6 latest recipes are passed to the view with eager loaded relations
it('passes exactly the 6 latest recipes with eager loaded relations', function () {
    $category = Category::first();

    // Create 7 recipes with increasing timestamps
    for ($i = 1; $i <= 7; $i++) {
        Recipe::factory()
            ->for(User::factory())
            ->create([
                'category_id' => $category->id,
                'created_at' => now()->addMinutes($i)
            ]);
    }

    $response = $this->get(route('home'));

    $recipesInView = $response->viewData('latestRecipes');

    // Controller should limit recipes collection to 6
    expect($recipesInView)->toHaveCount(6);

    // Check if the newest added recipe (the 7th one) is at the top
    $mostRecentRecipe = Recipe::orderBy('created_at', 'desc')->first();
    expect($recipesInView->first()->id)->toBe($mostRecentRecipe->id);

    // Check if the relations are loaded (no N+1 problem)
    expect($recipesInView->first()->relationLoaded('user'))->toBeTrue()
        ->and($recipesInView->first()->relationLoaded('category'))->toBeTrue()
        ->and($recipesInView->first()->relationLoaded('tags'))->toBeTrue();
});

// ==========================================
// 4. COMMENTS LOGIC
// ==========================================

// Verifies that only the 10 latest comments are passed to the view with eager loaded relations
it('passes exactly the 10 latest comments with eager loaded relations', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    // Create 11 comments with a time shift
    for ($i = 1; $i <= 11; $i++) {
        Comment::factory()->create([
            'recipe_id' => $recipe->id,
            'created_at' => now()->addMinutes($i)
        ]);
    }

    $response = $this->get(route('home'));

    $commentsInView = $response->viewData('recentComments');

    // The controller has a limit set to 10
    expect($commentsInView)->toHaveCount(10);

    // Check if the newest comment (January 11th) is the first one on the list
    $mostRecentComment = Comment::latest()->first();
    expect($commentsInView->first()->id)->toBe($mostRecentComment->id);

    // Check whether the comment relations are correctly pulled from the database
    expect($commentsInView->first()->relationLoaded('user'))->toBeTrue()
        ->and($commentsInView->first()->relationLoaded('recipe'))->toBeTrue()
        ->and($commentsInView->first()->relationLoaded('parent'))->toBeTrue();
});
