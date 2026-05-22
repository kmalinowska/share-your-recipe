<?php

use App\Models\Category;
use App\Models\User;
use App\Models\Recipe;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(function(){
    $this->seed(CategorySeeder::class);
});

/**
 * Guest / Regular User Permissions
 */

// UNIT TESTS

it('allows any logged-in user to view recipes and create new ones', function () {
    $user = User::factory()->create(['role' => 'user']);

    // Everyone can view the list and individual recipes
    expect($user->can('viewAny', Recipe::class))->toBeTrue()
        ->and($user->can('view', new Recipe()))->toBeTrue()
        ->and($user->can('create', Recipe::class))->toBeTrue();
});

/**
 * Creator Permissions (Ownership)
 */

it('allows owners to update or delete their own recipes', function () {
    $category = Category::first();
    $creator = User::factory()->create(['role' => 'user']);
    $recipe = Recipe::factory()->create(['user_id' => $creator->id, 'category_id' => $category->id]);

    expect($creator->can('update', $recipe))->toBeTrue()
        ->and($creator->can('delete', $recipe))->toBeTrue();
});

it('forbids users from updating or deleting recipes they do not own', function () {
    $category = Category::first();
    $user = User::factory()->create(['role' => 'user']);
    $otherUser = User::factory()->create(['role' => 'user']);
    $recipe = Recipe::factory()->create(['user_id' => $otherUser->id, 'category_id' => $category->id]);

    // Regular users cannot touch other people's recipes
    expect($user->can('update', $recipe))->toBeFalse()
        ->and($user->can('delete', $recipe))->toBeFalse();
});

/**
 * Admin Permissions (Superpowers)
 */

it('allows admins to update or delete any recipe', function () {
    $category = Category::first();
    $admin = User::factory()->admin()->create(); // Using the state we added to factory
    $recipe = Recipe::factory()->create(['category_id' => $category->id]); // Owned by someone else

    // Admin should pass all checks due to before() method
    expect($admin->can('update', $recipe))->toBeTrue()
        ->and($admin->can('delete', $recipe))->toBeTrue()
        ->and($admin->can('restore', $recipe))->toBeTrue();
});

it('forbids even owners from force deleting but allows admins', function () {
    $category = Category::first();
    $creator = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $recipe = Recipe::factory()->create(['user_id' => $creator->id, 'category_id' => $category->id]);

    // Owner should not be able to force delete based on our Policy
    expect($creator->can('forceDelete', $recipe))->toBeFalse();

    // Admin can do it because of before()
    expect($admin->can('forceDelete', $recipe))->toBeTrue();
});
