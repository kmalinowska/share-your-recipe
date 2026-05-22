<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Recipe;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(function(){
   $this->seed(CategorySeeder::class);
});

// ==========================================
// 1. STORE METHOD (Save comments)
// ==========================================
// ensure authenticated users can post comments
it('allows a logged in user to post a comment', function () {
    $category = Category::first();
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    $response = $this->actingAs($user)->post(route('comments.store', $recipe), [
        'content' => 'Tasty dish!',
    ]);

    $response->assertStatus(302); // check form redirection success
    $this->assertDatabaseHas('comments', [
        'content' => 'Tasty dish!',
        'user_id' => $user->id,
        'recipe_id' => $recipe->id,
        'guest_name' => null,
    ]); // check whether the comment was actually saved in the database
});

// ensure guests can post comments when guest_name is provided
it('allows a guest to post a comment with guest_name', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    $response = $this->post(route('comments.store', $recipe), [
        'content' => 'Nice recipe!',
        'guest_name' => 'Ania',
    ]);

    $response->assertStatus(302);
    $this->assertDatabaseHas('comments', [
        'content' => 'Nice recipe!',
        'guest_name' => 'Ania',
        'user_id' => null,
    ]);
});

// ensure nested replies are flattened to maintain single-level threading
it('flattens nested replies to maintain depth 1 (Flat Threading)', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);
    $rootComment = Comment::factory()->create(['recipe_id' => $recipe->id]);

    // creating a reply to the main comment (level 1)
    $reply = Comment::factory()->create([
        'recipe_id' => $recipe->id,
        'parent_id' => $rootComment->id
    ]);

    // attempting to reply to a reply (should flatten to rootComment)
    $response = $this->post(route('comments.store', $recipe), [
        'content' => 'Reply to reply',
        'parent_id' => $reply->id,
        'guest_name' => 'Tester',
    ]);

    $this->assertDatabaseHas('comments', [
        'content' => 'Reply to reply',
        'parent_id' => $rootComment->id, // Success
    ]);
});

// ==========================================
// 2. VALIDATION TESTS
// ==========================================

// ensure comment content is required
it('fails validation when content is missing', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    $response = $this->post(route('comments.store', $recipe), [
        'content' => '',
    ]);
    $response->assertSessionHasErrors('content');
});

// validate guest_name for unauthenticated users
it('requires guest_name when user is not logged in', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    $response = $this->post(route('comments.store', $recipe), [
        'content' => 'Sample Content',
        'guest_name' => '', // empty name
    ]);

    $response->assertSessionHasErrors('guest_name');
});

// ensure logged in users do not need to provide a guest name
it('does not require guest_name when user is logged in', function () {
    $category = Category::first();
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    $response = $this->actingAs($user)->post(route('comments.store', $recipe), [
        'content' => 'Authorized user comment without guest name field',
        'guest_name' => '', // send empty, but user is logged in
    ]);

    $response->assertSessionDoesntHaveErrors('guest_name');
});
