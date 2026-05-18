<?php

use App\Models\Comment;
use App\Models\Recipe;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(function(){
   $this->seed(CategorySeeder::class);
});

// ---- STORE ----
// ensure authenticated users can post comments
it('allows a logged in user to post a comment', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create();

    $response = $this->actingAs($user)->post(route('comments.store', $recipe), [
        'content' => 'Tasty dish!',
    ]);

    $response->assertStatus(302); // check form redirection success
    $this->assertDatabaseHas('comments', [
        'content' => 'Tasty dish!',
        'user_id' => $user->id,
        'recipe_id' => $recipe->id,
    ]); // check whether the comment was actually saved in the database
});

// ensure guests can post comments when guest_name is provided
it('allows a guest to post a comment with guest_name', function () {
    $recipe = Recipe::factory()->create();

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
    $recipe = Recipe::factory()->create();
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

// ensure comment content is required
it('fails validation when content is missing', function () {
    $recipe = Recipe::factory()->create();

    $response = $this->post(route('comments.store', $recipe), [
        'content' => '',
    ]);
    $response->assertSessionHasErrors('content');
});

// validate guest_name for unauthenticated users
it('requires guest_name when user is not logged in', function () {
    $recipe = Recipe::factory()->create();

    $response = $this->post(route('comments.store', $recipe), [
        'content' => 'Sample Content',
        'guest_name' => '', // empty name
    ]);

    $response->assertSessionHasErrors('guest_name');
});

