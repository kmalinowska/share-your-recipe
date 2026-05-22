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

it('can be authored by a user or a guest', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    $userComment = Comment::factory()->create(['recipe_id' => $recipe->id, 'guest_name' => null]);
    $guestComment = Comment::factory()->asGuest('Anna')->create(['recipe_id' => $recipe->id]);

    expect($userComment->user)->toBeInstanceOf(User::class)
        ->and($guestComment->user)->toBeNull()
        ->and($guestComment->guest_name)->toBe('Anna');
});

it('returns the correct author name via attribute', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);
    $user = User::factory()->create(['name' => 'Jan Kowalski']);

    $userComment = Comment::factory()->forUser($user)->create(['recipe_id' => $recipe->id]);
    $guestComment = Comment::factory()->asGuest('Beata')->create(['recipe_id' => $recipe->id]);
    $anonComment = Comment::factory()->create(['recipe_id' => $recipe->id, 'user_id' => null, 'guest_name' => null]);

    expect($userComment->author_name)->toBe('Jan Kowalski')
        ->and($guestComment->author_name)->toBe('Beata')
        ->and($anonComment->author_name)->toBe('Anonim');
});

it('can have replies (parent-child relationship)', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    $parent = Comment::factory()->create(['recipe_id' => $recipe->id]);
    $reply = Comment::factory()->isReply($parent)->create(['recipe_id' => $recipe->id]);

    expect($parent->replies)->toHaveCount(1)
        ->and($reply->parent->id)->toBe($parent->id)
        ->and($parent->replies->first()->id)->toBe($reply->id);
});

it('belongs to a recipe', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);
    $comment = Comment::factory()->create(['recipe_id' => $recipe->id]);

    expect($comment->recipe)->toBeInstanceOf(Recipe::class)
        ->and($comment->recipe->id)->toBe($recipe->id);
});
