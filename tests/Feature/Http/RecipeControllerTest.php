<?php

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\Ingredient;
use App\Models\Comment;
use App\Models\Favourite;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(function(){
    $this->seed(CategorySeeder::class);
});

// ==========================================
// 1. INDEX METHOD & SEARCHING
// ==========================================

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

// SEARCH: CATEGORY FILTER
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

// SEARCH: keyword & category
// verifies that keyword search and category filters work together correctly
it('filters recipes by keyword and category together', function () {

    $dessert = Category::first();
    $soup = Category::skip(1)->first();

    // SHOULD MATCH
    $matchingRecipe = Recipe::factory()->create([
        'title' => 'Chocolate Cake',
        'category_id' => $dessert->id,
    ]);

    // WRONG CATEGORY
    Recipe::factory()->create([
        'title' => 'Chocolate Soup',
        'category_id' => $soup->id,
    ]);

    // WRONG TITLE
    Recipe::factory()->create([
        'title' => 'Vanilla Cake',
        'category_id' => $dessert->id,
    ]);

    $response = $this->get(route('recipes.index', [
        'search' => 'Chocolate',
        'categories' => [$dessert->id],
    ]));

    $response->assertSee('Chocolate Cake');

    $response->assertDontSee('Chocolate Soup');
    $response->assertDontSee('Vanilla Cake');
});

// SEARCH: EMPTY STATE
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

// SORTING
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

// USER FAVOURITES
// verifies that favourite recipe IDs are passed to the view for authenticated users
it('passes user favourites array for logged in user', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create();
    // add user to favourites
    $user->favourites()->create(['recipe_id' => $recipe->id]);

    $response = $this->actingAs($user)->get(route('recipes.index'));

    $response->assertViewHas('userFavourites', function ($favourites) use ($recipe) {
        return in_array($recipe->id, $favourites);
    });
});

// GUEST FAVOURITES
// verifies that guests receive an empty favourites array
it('passes empty user favourites array for guests', function () {
    Recipe::factory()->create();

    $response = $this->get(route('recipes.index'));

    $response->assertViewHas('userFavourites', []);
});

// SELECTED CATEGORIES
// verifies that selected category filters are preserved in the view
it('passes selected categories back to view to retain checkbox states', function () {
    $category = Category::first();

    $response = $this->get(route('recipes.index', ['categories' => [$category->id]]));

    $response->assertViewHas('selectedCategories', function ($selected) use ($category) {
        return in_array($category->id, $selected);
    });
});


// ==========================================
// 2. SHOW METHOD
// ==========================================

// BASIC LOADING AND STATUS
// verifies that the recipe show page loads successfully
it('displays recipe show page successfully', function () {
    $recipe = Recipe::factory()->create();

    $response = $this->get(route('recipes.show', $recipe));

    $response->assertStatus(200);
    $response->assertViewIs('recipes.show');
});

// verifies that a non-existent recipe returns a 404 response
it('returns 404 for non-existent recipe slug', function() {
    $response = $this->get('/recipes/non-existent-slug');
    $response->assertStatus(404);
});

// verifies that the recipe model is passed correctly to the show view
it('passes recipe data to the show view', function () {
    $recipe = Recipe::factory()->create();

    $response = $this->get(route('recipes.show', $recipe));

    $response->assertViewHas('recipe', $recipe);
});

// Verifies that Route Model Binding (`{recipe:slug}`) works properly with raw URL strings
it('resolves the recipe correctly when hitting the raw slug URL directly', function () {
    $recipe = Recipe::factory()->create([
        'title' => 'Delicious Casserole',
        'slug' => 'delicious-casserole'
    ]);

    $response = $this->get('/recipes/delicious-casserole');

    $response->assertStatus(200);
    expect($response->viewData('recipe')->id)->toBe($recipe->id);
});

// RECIPE MODEL RELATIONS ($recipe->load)
// verifies that all required recipe relations are eager loaded
it('loads recipe relations on show page', function () {
    $existingCategory = Category::first();

    $recipe = Recipe::factory()
        ->hasAttached(
            Ingredient::factory()->count(2),
            ['quantity' => '100', 'unit' => 'g'],
            'ingredients'
        )
        ->hasTags(2)
        ->for(User::factory())
        ->for($existingCategory)
        ->create();

    $response = $this->get(route('recipes.show', $recipe));

    $loadedRecipe = $response->viewData('recipe');

    expect($loadedRecipe->relationLoaded('user'))->toBeTrue()
        ->and($loadedRecipe->relationLoaded('category'))->toBeTrue()
        ->and($loadedRecipe->relationLoaded('ingredients'))->toBeTrue()
        ->and($loadedRecipe->relationLoaded('tags'))->toBeTrue();
});

// verifies that recipe relations contain correct related data
it('passes accurate recipe data with relations to show view', function () {
    $recipe = Recipe::factory()->create();
    $ingredient = Ingredient::factory()->create(['name' => 'Salt']);
    $recipe->ingredients()->attach($ingredient->id, ['quantity' => '1', 'unit' => 'pinch']);

    $response = $this->get(route('recipes.show', $recipe));

    $recipeInView = $response->viewData('recipe');

    expect($recipeInView->id)->toBe($recipe->id)
        ->and($recipeInView->ingredients->first()->name)->toBe('Salt');
});

// COMMENT COLLECTION AND PAGINATION ($comments)
// verifies that recipe comments are displayed on the show page
it('shows comments on recipe page', function () {
    $recipe  = Recipe::factory()->create();

    Comment::factory()->create([
        'recipe_id' => $recipe->id,
        'content' => 'Amazing recipe!',
    ]);

    $response = $this->get(route('recipes.show', $recipe));

    $response->assertSee('Amazing recipe!');
});

// verifies that only root comments are included in the paginated comments collection
it('shows only root comments in comments collection', function () {
    $recipe = Recipe::factory()->create();

    $root = Comment::factory()->create([
        'recipe_id' => $recipe->id,
        'parent_id' => null,
    ]);

    $reply = Comment::factory()->create([
        'recipe_id' => $recipe->id,
        'parent_id' => $root->id,
    ]);

    $response = $this->get(route('recipes.show', $recipe));

    $comments = $response->viewData('comments');

    expect($comments->contains($root))->toBeTrue()
        ->and($comments->contains($reply))->toBeFalse();
});

// verifies that replies relation is eager loaded for root comments
it('loads replies relation for comments', function () {
    $recipe = Recipe::factory()->create();

    $root = Comment::factory()->create([
        'recipe_id' => $recipe->id
    ]);

    Comment::factory()->create([
        'recipe_id' => $recipe->id,
        'parent_id' => $root->id
    ]);

    $response = $this->get(route('recipes.show', $recipe));

    $comment = $response->viewData('comments')->first();

    expect($comment->relationLoaded('replies'))->toBeTrue();
});

// verifies that comments are ordered from newest to oldest
it('orders comments from newest to oldest', function () {
    $recipe = Recipe::factory()->create();

    Comment::factory()->create([
        'recipe_id' => $recipe->id,
        'content' => 'Old',
        'created_at' => now()->subDay()
    ]);

    Comment::factory()->create([
        'recipe_id' => $recipe->id,
        'content' => 'New',
        'created_at' => now()
    ]);

    $response = $this->get(route('recipes.show', $recipe));

    $response->assertSeeInOrder(['New', 'Old']);
});

// verifies that comments pagination is limited to 10 items per page
it('paginates comments with 10 items per page', function () {
    $recipe = Recipe::factory()->create();

    Comment::factory(12)->create([
        'recipe_id' => $recipe->id,
        'parent_id' => null,
    ]);

    $response = $this->get(route('recipes.show', $recipe));

    $comments = $response->viewData('comments');

    expect($comments->count())->toBe(10);
});

// COMMENT STATISTICS ($totalCommentsCount and $threadCount)
// verifies that total comments count includes both root comments and replies
it('passes total comments count including replies', function () {
    $recipe = Recipe::factory()->create();

    $root = Comment::factory()->create([
        'recipe_id' => $recipe->id
    ]);

    Comment::factory()->create([
        'recipe_id' => $recipe->id,
        'parent_id' => $root->id
    ]);

    $response = $this->get(route('recipes.show', $recipe));

    $response->assertViewHas('totalCommentsCount', 2);
});

// verifies that thread count includes only root comments
it('passes thread count with only root comments', function () {
    $recipe = Recipe::factory()->create();

    $root = Comment::factory()->create([
        'recipe_id' => $recipe->id
    ]);

    Comment::factory()->create([
        'recipe_id' => $recipe->id,
        'parent_id' => $root->id
    ]);

    $response = $this->get(route('recipes.show', $recipe));

    $response->assertViewHas('threadCount', 1);
});

// FAVOURITES ($userFavourites)
// verifies that authenticated users receive their favourite recipe IDs
it('passes user favourites ids for authenticated user', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create();

    Favourite::factory()->create([
        'user_id' => $user->id,
        'recipe_id' => $recipe->id
    ]);

    $response = $this->actingAs($user)
        ->get(route('recipes.show', $recipe));

    $response->assertViewHas('userFavourites', function ($favourites) use ($recipe) {
        return in_array($recipe->id, $favourites);
    });
});

// verifies that guests receive an empty favourites array
it('passes empty favourites array for guests', function () {
    $recipe = Recipe::factory()->create();

    $response = $this->get(route('recipes.show', $recipe));

    $response->assertViewHas('userFavourites', []);
});

// ==========================================
// tagIndex METHOD
// ==========================================

it('shows only recipes assigned to the selected tag', function () {
    $tag = Tag::factory()->create(['name' => 'Keto', 'slug' => 'keto']);

    $recipeWithTag = Recipe::factory()->create([
        'title' => 'Keto Salad'
    ]);

    $recipeWithoutTag = Recipe::factory()->create([
        'title' => 'Pasta'
    ]);

    $recipeWithTag->tags()->attach($tag->id);

    $response = $this->get(route('recipes.tag', $tag->slug));

    $response->assertStatus(200);

    // extract the recipes sent to the view
    $recipesInView = $response->viewData('recipes');

    // check entities in the database instead of HTML code
    expect($recipesInView->contains($recipeWithTag))->toBeTrue()
        ->and($recipesInView->contains($recipeWithoutTag))->toBeFalse();
});

// Verifies that 'user' and 'category' relations are eager loaded to prevent N+1 query issues on the tag page
it('eager loads category and user relations for tagged recipes', function () {
    // use the categories from the seeder to avoid uniqueness conflicts in PostgreSQL
    $existingCategory = Category::first();
    $tag = Tag::factory()->create();

    $recipe = Recipe::factory()
        ->for(User::factory())
        ->for($existingCategory)
        ->create();

    $recipe->tags()->attach($tag);

    $response = $this->get(route('recipes.tag', $tag->slug));

    $loadedRecipe = $response->viewData('recipes')->first();

    expect($loadedRecipe->relationLoaded('user'))->toBeTrue()
        ->and($loadedRecipe->relationLoaded('category'))->toBeTrue();
});

// Verifies that recipes associated with a specific tag are ordered from newest to oldest (latest first)
it('orders tagged recipes from newest to oldest', function () {
    $existingCategory = Category::first();
    $tag = Tag::factory()->create();

    $oldRecipe = Recipe::factory()->create([
        'category_id' => $existingCategory->id,
        'title' => 'Old Tagged Recipe',
        'created_at' => now()->subDay()
    ]);

    $newRecipe = Recipe::factory()->create([
        'category_id' => $existingCategory->id,
        'title' => 'New Tagged Recipe',
        'created_at' => now()
    ]);

    $oldRecipe->tags()->attach($tag);
    $newRecipe->tags()->attach($tag);

    $response = $this->get(route('recipes.tag', $tag->slug));

    $response->assertStatus(200);

    $recipesInView = $response->viewData('recipes');

    expect($recipesInView->first()->id)->toBe($newRecipe->id)
        ->and($recipesInView->last()->id)->toBe($oldRecipe->id);
});

// Verifies that the tagged recipes collection is correctly paginated with a limit of 12 items per page
it('paginates tagged recipes with 12 items per page', function () {
    $existingCategory = Category::first();
    $tag = Tag::factory()->create();

    // create 15 recipes assigned to a safe, existing category
    $recipes = Recipe::factory(15)->create([
        'category_id' => $existingCategory->id
    ]);

    foreach ($recipes as $recipe) {
        $recipe->tags()->attach($tag);
    }

    $response = $this->get(route('recipes.tag', $tag->slug));

    // check if there are exactly 12 items on the first page (according to the controller)
    expect($response->viewData('recipes')->count())->toBe(12);
});

// Verifies that the correct, formatted dynamic title string is passed to the tag index view
it('passes dynamic tag title to the view', function () {
    $tag = Tag::factory()->create([
        'name' => 'Keto'
    ]);

    $response = $this->get(route('recipes.tag', $tag->slug));

    $response->assertViewHas(
        'title',
        'Recipes with tag: #Keto'
    );
});

// Verifies that the active tag slug is passed to the view to allow frontend highlighting
it('passes active tag slug to the view', function () {
    $tag = Tag::factory()->create();

    $response = $this->get(route('recipes.tag', $tag->slug));

    $response->assertStatus(200);
    $response->assertViewHas('activeTag', $tag->slug);
});

// FAVOURITES ON THE TAG PAGE ($userFavourites)
// verifies that authenticated users receive their favourite recipe IDs on the tag page
it('passes user favourites ids for authenticated user on tag page', function () {
    $existingCategory = Category::first();
    $user = User::factory()->create();
    $tag = Tag::factory()->create();

    $recipe = Recipe::factory()->create(['category_id' => $existingCategory->id]);

    Favourite::factory()->create([
        'user_id' => $user->id,
        'recipe_id' => $recipe->id
    ]);

    $response = $this->actingAs($user)
        ->get(route('recipes.tag', $tag->slug));

    $response->assertStatus(200);
    $response->assertViewHas('userFavourites', function ($favourites) use ($recipe) {
        return in_array($recipe->id, $favourites);
    });
});

// verifies that guests receive an empty favourites array on the tag page
it('passes empty favourites array for guests on tag page', function () {
    $tag = Tag::factory()->create();

    $response = $this->get(route('recipes.tag', $tag->slug));

    $response->assertStatus(200);
    $response->assertViewHas('userFavourites', []);
});
