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
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

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

// VITE PLACEHOLDER CHECK (INDEX)
// Verifies that a recipe with a Vite asset path renders correctly on the index page
it('renders a local Vite placeholder asset correctly on the index page', function () {
    $recipe = Recipe::factory()->create([
        'title' => 'Vite Placeholder Dish',
        'image_path' => 'resources/images/placeholders/default-recipe.jpg'
    ]);

    $response = $this->get(route('recipes.index'));

    $response->assertStatus(200);
    $response->assertSee(Vite::asset('resources/images/placeholders/default-recipe.jpg'), false);
    $response->assertDontSee('storage/resources/images/placeholders/default-recipe.jpg');
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
    $this->withoutExceptionHandling();
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

// VITE PLACEHOLDER CHECK (SHOW)
// Verifies that a recipe with a Vite asset path renders correctly on the show page
it('renders a local Vite placeholder asset correctly on the show page', function () {
    $recipe = Recipe::factory()->create([
        'title' => 'Vite Placeholder Show Dish',
        'image_path' => 'resources/images/placeholders/default-recipe.jpg'
    ]);

    $response = $this->get(route('recipes.show', $recipe));

    $response->assertStatus(200);
    $response->assertSee(Vite::asset('resources/images/placeholders/default-recipe.jpg'), false);
    $response->assertDontSee('storage/resources/images/placeholders/default-recipe.jpg');
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
// 3. tagIndex METHOD
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

// ==========================================
// 4. indexByCategory METHOD
// ==========================================
// Verifies that only recipes from the selected category are displayed, and currentCategory is passed to the view
it('filters recipes by category and passes currentCategory to view', function () {
    $category = Category::first();
    $otherCategory = Category::skip(1)->first();

    $recipeInCat = Recipe::factory()->create(['category_id' => $category->id]);
    $recipeOutCat = Recipe::factory()->create(['category_id' => $otherCategory->id]);

    $response = $this->get(route('recipes.category', $category->slug));

    $response->assertStatus(200);

    $recipesInView = $response->viewData('recipes');
    expect($recipesInView->contains($recipeInCat))->toBeTrue()
        ->and($recipesInView->contains($recipeOutCat))->toBeFalse();

    $currentCategoryInView = $response->viewData('currentCategory');
    expect($currentCategoryInView->id)->toBe($category->id);
});

// Verifies that 'user', 'category', and 'tags' relations are eager loaded to optimize database queries
it('eager loads relations for category recipes', function () {
    $category = Category::first();

    $recipe = Recipe::factory()
        ->for(User::factory())
        ->for($category)
        ->hasTags(2)
        ->create();

    $response = $this->get(route('recipes.category', $category->slug));

    $loadedRecipe = $response->viewData('recipes')->first();

    expect($loadedRecipe->relationLoaded('user'))->toBeTrue()
        ->and($loadedRecipe->relationLoaded('category'))->toBeTrue()
        ->and($loadedRecipe->relationLoaded('tags'))->toBeTrue();
});

// Verifies that the category recipes collection is correctly paginated with a limit of 12 items per page
it('paginates category recipes with 12 items per page', function () {
    $category = Category::first();

    Recipe::factory(15)->create([
        'category_id' => $category->id
    ]);

    $response = $this->get(route('recipes.category', $category->slug));

    expect($response->viewData('recipes')->count())->toBe(12);
});

// Verifies that recipes within a category are ordered chronologically from newest to oldest
it('orders category recipes from newest to oldest', function () {
    $category = Category::first();

    $oldRecipe = Recipe::factory()->create([
        'category_id' => $category->id,
        'title' => 'Unique Old Category Recipe',
        'created_at' => now()->subDays(2),
    ]);

    $newRecipe = Recipe::factory()->create([
        'category_id' => $category->id,
        'title' => 'Unique New Category Recipe',
        'created_at' => now(),
    ]);

    $response = $this->get(route('recipes.category', $category->slug));

    // verify collection order
    $recipes = $response->viewData('recipes');
    expect($recipes->first()->id)->toBe($newRecipe->id)
        ->and($recipes->last()->id)->toBe($oldRecipe->id);

    // verify rendered HTML order
    $response->assertSeeInOrder([
        'Unique New Category Recipe',
        'Unique Old Category Recipe'
    ]);
});

// ==========================================
// 5. CREATE METHOD
// ==========================================

// GUEST CREATE
// verifies that guests cannot access the recipe creation form
it('redirects guests to login page when attempting to view create form', function () {
    $response = $this->get(route('recipes.create'));

    $response->assertRedirect(route('login'));
});

// AUTH USER CREATE
// verifies that authenticated users can view the creation form with categories and tags
it('displays recipe creation form for authenticated users with sorted data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('recipes.create'));

    $response->assertStatus(200);
    $response->assertViewIs('recipes.create');

    // verifies that categories and tags are injected into the view
    $response->assertViewHas('categories');
    $response->assertViewHas('tags');
});

// ==========================================
// 6. STORE METHOD
// ==========================================

// GUEST STORE
// verifies that guests cannot store recipes
it('redirects guests to login page when attempting to store a recipe', function () {
    $response = $this->post(route('recipes.store'), []);

    $response->assertRedirect(route('login'));
});

// VALIDATION
// verifies that recipe creation requires mandatory fields
it('requires validation for mandatory fields on storing recipe', function () {
    $user = User::factory()->create();

    // Sending empty data
    $response = $this->actingAs($user)->post(route('recipes.store'), []);

    $response->assertSessionHasErrors([
        'title',
        'preparation_time',
        'category_id',
        'steps',
        'ingredients'
    ]);
});

// SUCCESSFUL STORE
// verifies that authenticated users can successfully create a recipe with image, ingredients, and tags
it('successfully stores a valid recipe with image upload, ingredients, and tags', function () {
    // 1. Setup fake disk storage
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::first(); // using seeder category
    $tag1 = Tag::factory()->create();
    $tag2 = Tag::factory()->create();

    // Create a mock image file
    $fakeImage = UploadedFile::fake()->create('strawberry-pancake.jpg', 100);

    // Prepare complete payload matching Alpine.js and Request structures
    $recipeData = [
        'title' => 'Strawberry Pancake',
        'preparation_time' => 25,
        'category_id' => $category->id,
        'image_path' => $fakeImage,
        'steps' => [
            'Mix flour, milk, and eggs together.',
            'Fry on a hot pan until golden brown.',
            'Serve with fresh strawberries on top.'
        ],
        'ingredients' => [
            ['name' => 'Strawberries', 'quantity' => '150', 'unit' => 'g'],
            ['name' => 'Milk', 'quantity' => '1', 'unit' => 'cup']
        ],
        'tags' => [$tag1->id, $tag2->id]
    ];

    // 2. Fire Request
    $response = $this->actingAs($user)->post(route('recipes.store'), $recipeData);

    // 3. Assertions
    $recipe = Recipe::firstWhere('title', 'Strawberry Pancake');

    // Redirect check
    $response->assertRedirect(route('recipes.show', $recipe));
    $response->assertSessionHas('success', 'Recipe created successfully!');

    // Base Database check
    $this->assertDatabaseHas('recipes', [
        'id' => $recipe->id,
        'title' => 'Strawberry Pancake',
        'preparation_time' => 25,
        'category_id' => $category->id,
        'user_id' => $user->id,
    ]);

    // JSON Cast array check
    expect($recipe->preparation)->toBe([
        'Mix flour, milk, and eggs together.',
        'Fry on a hot pan until golden brown.',
        'Serve with fresh strawberries on top.'
    ]);

    // Pivot tables and relations check
    expect($recipe->ingredients)->toHaveCount(2)
        ->and($recipe->tags)->toHaveCount(2);

    $this->assertDatabaseHas('recipe_ingredients', [
        'recipe_id' => $recipe->id,
        'ingredient_id' => $recipe->ingredients->first()->id,
        'quantity' => '150',
        'unit' => 'g'
    ]);

    // Image Upload disk check
    expect($recipe->image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($recipe->image_path);
    expect($recipe->image_path)->toStartWith('recipe/');
});

// ==========================================
// 7. UPDATE METHOD
// ==========================================

// GUEST UPDATE
it('redirects guests to login page when attempting to update a recipe', function () {
    $recipe = Recipe::factory()->create();

    $response = $this->put(route('recipes.update', $recipe), []);

    $response->assertRedirect(route('login'));
});

// AUTHORIZATION
it('returns 403 when a user attempts to update someone elses recipe', function () {
    $author = User::factory()->create();
    $stranger = User::factory()->create();
    $recipe = Recipe::factory()->create(['user_id' => $author->id]);

    $response = $this->actingAs($stranger)->put(route('recipes.update', $recipe), [
        'title' => 'Malicious Update',
    ]);

    $response->assertStatus(403);
});

// VALIDATION
it('requires validation for mandatory fields on updating recipe', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('recipes.update', $recipe), []);

    $response->assertSessionHasErrors([
        'title',
        'preparation_time',
        'category_id',
        'steps',
        'ingredients'
    ]);
});

// SUCCESSFUL UPDATE (WITH IMAGE & TOGGLES & SLUG)
it('successfully updates a recipe, regenerates slug, updates checkboxes, and manages storage images', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::first();
    $tag = Tag::factory()->create();

    // 1. Create a recipe with an existing old image and comments enabled
    $recipe = Recipe::factory()->create([
        'user_id' => $user->id,
        'title' => 'Old Title',
        'slug' => 'old-title',
        'image_path' => 'recipes/old-image.jpg',
        'is_commentable' => true,
    ]);

    // Physically seed a file into the fake storage to ensure it gets deleted later
    Storage::disk('public')->put('recipes/old-image.jpg', 'fake content');

    // Prepare new data payload (disabling comments, updating title, uploading a new image)
    $newFakeImage = UploadedFile::fake()->create('new-pancake.jpg', 100);

    $updateData = [
        'title' => 'New Delicious Title',
        'preparation_time' => 40,
        'category_id' => $category->id,
        'image_path' => $newFakeImage,
        'steps' => ['Step 1: Done', 'Step 2: Done'],
        'ingredients' => [
            ['name' => 'Flour', 'quantity' => '200', 'unit' => 'g']
        ],
        'tags' => [$tag->id],
        // Leaving out 'is_commentable' simulates unchecking a checkbox in HTML forms
    ];

    // 2. Execute the request
    $response = $this->actingAs($user)->put(route('recipes.update', $recipe), $updateData);

    // 3. Assertions
    $recipe->refresh();

    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('success', 'Recipe updated successfully!');

    // Check database fields and automatic slug regeneration
    expect($recipe->title)->toBe('New Delicious Title')
        ->and($recipe->slug)->toBe('new-delicious-title')
        ->and($recipe->is_commentable)->toBeFalse() // Verify unchecked checkbox behavior
        ->and($recipe->preparation)->toBe(['Step 1: Done', 'Step 2: Done']);

    // Check that the old image was deleted and the new one was stored successfully
    Storage::disk('public')->assertMissing('recipes/old-image.jpg');
    Storage::disk('public')->assertExists($recipe->image_path);

    // Check relationship synchronization (Pivot table mapping)
    expect($recipe->tags->contains($tag))->toBeTrue()
        ->and($recipe->ingredients->first()->name)->toBe('flour'); // Matches Str::lower() in controller
});


// ==========================================
// 8. DESTROY METHOD
// ==========================================

// GUEST DESTROY
it('redirects guests to login page when attempting to delete a recipe', function () {
    $recipe = Recipe::factory()->create();

    $response = $this->delete(route('recipes.destroy', $recipe));

    $response->assertRedirect(route('login'));
});

// AUTHORIZATION
it('returns 403 when a user attempts to delete someone elses recipe', function () {
    $author = User::factory()->create();
    $stranger = User::factory()->create();
    $recipe = Recipe::factory()->create(['user_id' => $author->id]);

    $response = $this->actingAs($stranger)->delete(route('recipes.destroy', $recipe));

    $response->assertStatus(403);
    $this->assertNotNull($recipe->fresh());
});

// SUCCESSFUL DESTROY
it('successfully deletes a recipe and removes its image from disk', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $recipe = Recipe::factory()->create([
        'user_id' => $user->id,
        'image_path' => 'recipes/to-be-deleted.jpg'
    ]);

    Storage::disk('public')->put('recipes/to-be-deleted.jpg', 'fake content');

    $response = $this->actingAs($user)->delete(route('recipes.destroy', $recipe));

    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('success', 'Recipe deleted successfully.');

    // Verify database record deletion and storage cleanup
    $this->assertNull($recipe->fresh());
    Storage::disk('public')->assertMissing('recipes/to-be-deleted.jpg');
});
