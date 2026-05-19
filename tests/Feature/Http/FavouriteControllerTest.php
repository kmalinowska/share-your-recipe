<?php

use App\Models\Favourite;
use App\Models\Recipe;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(function(){
    $this->seed(CategorySeeder::class);
});

// ------- INDEX --------
// Ensure unauthenticated users cannot access favourites index
it('redirects guests to login when accessing favourites index', function () {
    $response = $this->get(route('favourites.index'));
    $response->assertRedirect(route('login'));
});

// Ensure favourite recipes are displayed for logged in users
it('shows favourite recipes for the logged in user on the favourites index page', function() {
   $user = User::factory()->create();
   $recipe = Recipe::factory()->create(['title' => 'Favourite Dish']);

   Favourite::factory()->create([
       'user_id' => $user->id,
       'recipe_id' => $recipe->id
   ]);

   $response = $this->actingAs($user)->get(route('favourites.index'));
   $response->assertStatus(200);
   $response->assertSee('Favourite Dish');
   $response->assertViewIs('favourites.index');
   $response->assertViewHas('recipes');
});

// Ensure favourites are ordered by latest first
it('shows favourite recipes from newest to oldest', function () {

    $user = User::factory()->create();
    $olderRecipe = Recipe::factory()->create(['title' => 'Older Recipe']);
    $newerRecipe = Recipe::factory()->create(['title' => 'Newer Recipe']);

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

    $response->assertSeeInOrder([
        'Newer Recipe',
        'Older Recipe',
    ]);
});

// Pagination tests
it('paginates favourite recipes with 12 items per page', function () {

    $user = User::factory()->create();
    $recipes = Recipe::factory()->count(13)->create();

    foreach ($recipes as $recipe) {
        Favourite::factory()->create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    $response = $this->actingAs($user)
        ->get(route('favourites.index'));

    $response->assertStatus(200);

    // first 12 should be visible
    foreach ($recipes->take(12) as $recipe) {
        $response->assertSee($recipe->title);
    }

    // 13th should NOT be on first page
    $response->assertDontSee($recipes[12]->title);
});

it('shows remaining favourite recipes on the second page', function () {

    $user = User::factory()->create();

    $recipes = Recipe::factory()->count(13)->create();

    foreach ($recipes as $recipe) {
        Favourite::factory()->create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    $response = $this->actingAs($user)
        ->get(route('favourites.index', ['page' => 2]));

    $response->assertStatus(200);

    $response->assertSee($recipes[12]->title);
});

// Ensure user favourite IDs are available in the view
it('passes user favourites IDs to the view', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create();
    Favourite::factory()->create(['user_id' => $user->id, 'recipe_id' => $recipe->id]);

    $response = $this->actingAs($user)->get(route('favourites.index'));

    $response->assertViewHas('userFavourites', function ($favs) use ($recipe) {
        return in_array($recipe->id, $favs);
    });
});

// ---- TOGGLE (Click on heart) ----
// ensures a favourite record is created for authenticated users
it('adds a recipe to favourites if not already there', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create();

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
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create();

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

// verifies that the favourites toggle correctly adds and removes a recipe
it('toggles a recipe in favourites', function() {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create();

    // Action 1. Add recipe to favourites
    $response = $this->actingAs($user)
        ->post(route('favourites.toggle', $recipe));
    $response->assertSessionHas('success', 'Added to favourites!');

    $this->assertDatabaseHas('favourites', [
        'user_id' => $user->id,
        'recipe_id' => $recipe->id
    ]);

    // Action 2. Remove recipe from favourites (toggle)
    $response = $this->actingAs($user)
        ->post(route('favourites.toggle', $recipe));
    $response->assertSessionHas('success', 'Removed from favourites.');

    $this->assertDatabaseMissing('favourites', [
        'user_id' => $user->id,
        'recipe_id' => $recipe->id
    ]);
});

// ensures unauthenticated users are redirected to login
it('prevents guests from toggling favourites', function () {
    $recipe = Recipe::factory()->create();

    $response = $this->post(route('favourites.toggle', $recipe));

    $response->assertRedirect(route('login'));
});

// verifies that users can only see their own favourite recipes
it('shows only user favourite recipes on the index page', function() {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $favouriteRecipe = Recipe::factory()->create(['title' => 'My favourite']);
    $otherRecipe = Recipe::factory()->create(['title' => 'Other Recipe']);

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
    $response->assertSee('My favourite');
    $response->assertDontSee('Other Recipe');
});
