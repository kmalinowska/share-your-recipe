<?php

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\Ingredient;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(function(){
    $this->seed(CategorySeeder::class);
});

// ---- INDEX & SEARCHING (List of recipes) ----

// INDEX
// verifies that the recipes index page loads successfully
it('displays recipes index page successfully', function () {
    $response = $this->get(route('recipes.index'));

    $response->assertStatus(200);
    $response->assertViewIs('recipes.index');
});

// verifies that all recipe categories are passed to the index view
it('passes all categories to the view', function(){
    // The database was added in beforeEach (9 categories)
    $response = $this->get(route('recipes.index'));

    $response->assertViewHas('categories', function($categories) {
        return $categories->count() === 9; // check if it has downloaded all the files exactly
    });
});

// verifies that recipes are available on the index page
it('shows list of recipes on index page', function(){
    Recipe::factory(3)->create();

    $response = $this->get(route('recipes.index'));

    $response->assertViewHas('recipes');
});

// DEFAULT STATE
// verifies that recipes are displayed when no search filters are applied
it('shows recipes when no filters are applied (default state)', function () {
    Recipe::factory()->create(['title' => 'Chocolate Brownie']);

    $response = $this->get(route('recipes.index'));

    $response->assertSee('Chocolate Brownie');
});

// SEARCH: TITLE
// verifies that recipes can be filtered by title keyword (case insensitive)
it('filters recipes by search keyword in title', function(){
    Recipe::factory()->create(['title' => 'Chocolate Brownie']);
    Recipe::factory()->create(['title' => 'Caesar Salad']);

    $response = $this->get(route('recipes.index', ['search' => 'CHOCO'])); // Test also Case Insensitivity!

    $response->assertSee('Chocolate Brownie');
    $response->assertDontSee('Caesar Salad');
});

// SEARCH: PREPARATION
// verifies that recipes can be filtered by preparation instructions
it('filters recipes by search keyword in preparation text', function () {
    Recipe::factory()->create(['title' => 'Recipe A', 'preparation' => 'Bake it in the oven']);
    Recipe::factory()->create(['title' => 'Recipe B', 'preparation' => 'Boil it in water']);

    $response = $this->get(route('recipes.index', ['search' => 'bake']));

    $response->assertSee('Recipe A');
    $response->assertDontSee('Recipe B');
});

// SEARCH: TAGS
// verifies that recipes can be filtered by related tags
it('filters recipes by search keyword in tags', function () {
    $tag = Tag::factory()->create(['name' => 'Keto']);

    $ketoRecipe = Recipe::factory()->create(['title' => 'Avocado Bowl', 'preparation' => 'Cut and serve.']);
    $ketoRecipe->tags()->attach($tag->id);

    $normalRecipe = Recipe::factory()->create(['title' => 'Pasta Carbonara']);

    $response = $this->get(route('recipes.index', ['search' => 'keto']));

    $response->assertSee('Avocado Bowl');
    $response->assertDontSee('Pasta Carbonara');
});

// SEARCH: INGREDIENTS
// verifies that recipes can be filtered by related ingredients
it('filters recipes by search keyword in ingredients', function () {
    // 1. Create unique ingredient
    $ingredient = Ingredient::factory()->create(['name' => 'Cinnamon']);

    // 2. Create a recipe with this ingredient (no word Cinnamon in the title/description)
    $christmasPie = Recipe::factory()->create(['title' => 'Sweet Pastry', 'preparation' => 'Mix and bake.']);
    $christmasPie->ingredients()->attach($ingredient->id, ['quantity' => '1', 'unit' => 'tsp']);

    // 3. Create a recipe without this ingredient
    $saltySoup = Recipe::factory()->create(['title' => 'Tomato Soup']);

    // 4. Search the word "cinnamon" in lowercase letters
    $response = $this->get(route('recipes.index', ['search' => 'cinnamon']));

    // 5. Check if the relationship was filtered correctly
    $response->assertSee('Sweet Pastry');
    $response->assertDontSee('Tomato Soup');
});

// CATEGORY FILTER
// verifies that recipes can be filtered by selected category
it('filters recipes by single category selection', function () {
    // download categories from the seeder
    $categoryA = Category::first();
    $categoryB = Category::skip(1)->first();

    $recipeA = Recipe::factory()->create(['category_id' => $categoryA->id, 'title' => 'Category A Dish']);
    $recipeB = Recipe::factory()->create(['category_id' => $categoryB->id, 'title' => 'Category B Dish']);

    $response = $this->get(route('recipes.index', ['categories' => [$categoryA->id]]));

    // precisely check the data from the controller
    $recipesInView = $response->viewData('recipes');
    expect($recipesInView->contains($recipeA))->toBeTrue()
        ->and($recipesInView->contains($recipeB))->toBeFalse();

    // check if the view has not crashed and render the title
    $response->assertSee('Category A Dish');
    $response->assertDontSee('Category B Dish');
});

// EMPTY STATE
// verifies that no recipes are shown when search results are empty
it('shows empty state message when no recipes match search criteria', function () {
    Recipe::factory()->create(['title' => 'Chocolate Brownie']);

    $response = $this->get(route('recipes.index', ['search' => 'non-existent-keyword']));

    $response->assertDontSee('Chocolate Brownie');
    $response->assertStatus(200);
});

// PAGINATION
// verifies that recipe pagination limits results to 4 items per page
it('paginates recipes with exactly 4 items per page', function () {
    Recipe::factory(6)->create();

    $response = $this->get(route('recipes.index'));

    expect($response->viewData('recipes')->count())->toBe(4);
});

// verifies that recipes are ordered from newest to oldest
it('orders recipes correctly from latest to oldest', function () {
    $oldRecipe = Recipe::factory()->create(['title' => 'Old Recipe', 'created_at' => now()->subDays(2)]);
    $newRecipe = Recipe::factory()->create(['title' => 'New Recipe', 'created_at' => now()]);

    $response = $this->get(route('recipes.index'));

    // 1. Carefully examine the Eloquent collection
    $recipes = $response->viewData('recipes');
    expect($recipes->first()->id)->toBe($newRecipe->id);

    // 2. Check whether the user sees them in the correct order on the screen
    $response->assertSeeInOrder(['New Recipe', 'Old Recipe']);
});
