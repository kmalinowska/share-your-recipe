<?php

use App\Models\Category;
use App\Models\Favourite;
use App\Models\Recipe;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(function(){
    $this->seed(CategorySeeder::class);
});

// ==========================================
// 1. INDEX METHOD
// ==========================================
// Ensure unauthenticated users cannot access favourites index
it('redirects guests to login when accessing favourites index', function () {
    $response = $this->get(route('favourites.index'));
    $response->assertRedirect(route('login'));
});

// Ensure favourite recipes are displayed for logged in users
it('shows favourite recipes for the logged in user on the favourites index page', function() {
    $category = Category::first();
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create(['category_id' => $category->id, 'title' => 'Unique Favourite Dish']);

   Favourite::factory()->create([
       'user_id' => $user->id,
       'recipe_id' => $recipe->id
   ]);

   $response = $this->actingAs($user)->get(route('favourites.index'));
   $response->assertStatus(200);
   $response->assertViewIs('favourites.index');
   $recipesInView = $response->viewData('recipes');
   expect($recipesInView->contains($recipe))->toBetrue();
});

// Ensure favourites are ordered by latest first
it('shows favourite recipes from newest to oldest', function () {
    $category = Category::first();
    $user = User::factory()->create();
    $olderRecipe = Recipe::factory()->create(['category_id' => $category->id, 'title' => 'Older Recipe']);
    $newerRecipe = Recipe::factory()->create(['category_id' => $category->id, 'title' => 'Newer Recipe']);

    Favourite::factory()->create([
        'user_id' => $user->id,
        'recipe_id' => $olderRecipe->id,
        'created_at' => now()->subDay(),
    ]);

    Favourite::factory()->create([
        'user_id' => $user->id,
        'recipe_id' => $newerRecipe->id,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('favourites.index'));

    $response->assertStatus(200);

    $recipesInView = $response->viewData('recipes');
    expect($recipesInView->first()->id)->toBe($newerRecipe->id)
        ->and($recipesInView->last()->id)->toBe($olderRecipe->id);
});

// Pagination tests
it('paginates favourite recipes with 12 items per page', function () {
    $category = Category::first();
    $user = User::factory()->create();
    $recipes = Recipe::factory()->count(13)->create(['category_id' => $category->id]);

    foreach ($recipes as $recipe) {
        Favourite::factory()->create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    $response = $this->actingAs($user)
        ->get(route('favourites.index'));

    $response->assertStatus(200);
    expect($response->viewData('recipes')->count())->toBe(12);
});

it('shows remaining favourite recipes on the second page', function () {
    $category = Category::first();
    $user = User::factory()->create();

    $recipes = Recipe::factory()->count(13)->create(['category_id' => $category->id]);

    foreach ($recipes as $recipe) {
        Favourite::factory()->create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    $response = $this->actingAs($user)
        ->get(route('favourites.index', ['page' => 2]));

    $response->assertStatus(200);
    expect($response->viewData('recipes')->count())->toBe(1);
});

// Ensure user favourite IDs are available in the view
it('passes user favourites IDs to the view', function () {
    $category = Category::first();
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);
    Favourite::factory()->create(['user_id' => $user->id, 'recipe_id' => $recipe->id]);

    $response = $this->actingAs($user)->get(route('favourites.index'));

    $response->assertViewHas('userFavourites', function ($favs) use ($recipe) {
        return in_array($recipe->id, $favs);
    });
});

// verifies that users can only see their own favourite recipes
it('shows only user favourite recipes on the index page', function() {
    $category = Category::first();
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $favouriteRecipe = Recipe::factory()->create(['category_id' => $category->id]);
    $otherRecipe = Recipe::factory()->create(['category_id' => $category->id]);

    Favourite::factory()->create([
        'user_id' => $user->id,
        'recipe_id' => $favouriteRecipe->id
    ]);

    Favourite::factory()->create([
        'user_id' => $otherUser->id,
        'recipe_id' => $otherRecipe->id
    ]);

    $response = $this->actingAs($user)->get(route('favourites.index'));

    $response->assertStatus(200);
    $recipesInView = $response->viewData('recipes');
    expect($recipesInView->contains($favouriteRecipe))->toBeTrue()
        ->and($recipesInView->contains($otherRecipe))->toBeFalse();
});

// ==========================================
// 2. TOGGLE METHOD (Heart Click)
// ==========================================
// ensures a favourite record is created for authenticated users
it('adds a recipe to favourites if not already there', function () {
    $category = Category::first();
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    $response = $this->actingAs($user)->post(route('favourites.toggle', $recipe));

    $response->assertStatus(302);
    $this->assertDatabaseHas('favourites', [
        'user_id' => $user->id,
        'recipe_id' => $recipe->id
    ]);

    // ensures only one favourite record exists
    $this->assertDatabaseCount('favourites', 1);
    $response->assertSessionHas('success', 'Added to favourites!');
});

// ensures an existing favourite record is deleted when toggled again
it('removes a recipe from favourites if it is already there', function () {
    $category = Category::first();
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    Favourite::factory()->create([
        'user_id' => $user->id,
        'recipe_id' => $recipe->id
    ]);

    $response = $this->actingAs($user)->post(route('favourites.toggle', $recipe));

    $this->assertDatabaseMissing('favourites', [
        'user_id' => $user->id,
        'recipe_id' => $recipe->id
    ]);
    $response->assertSessionHas('success', 'Removed from favourites.');
});

// ensures unauthenticated users are redirected to login
it('prevents guests from toggling favourites', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    $response = $this->post(route('favourites.toggle', $recipe));

    $response->assertRedirect(route('login'));
});
