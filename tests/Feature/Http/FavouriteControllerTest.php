<?php

use App\Models\Favourite;
use App\Models\Recipe;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
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

// ------- TOOGLE --------
