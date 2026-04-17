<?php

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/**
 * Model Logic & Security
 */

it('generates a valid uuid on creation', function () {
    // Act: Create a user via factory
    $user = User::factory()->create();

    // Assert: Check if ID is a valid UUID string (36 chars)
    expect($user->id)->toBeString()->toHaveLength(36);
});

it('hashes the password automatically via model casts', function () {
    // Act: Create a user with a plain text password
    $user = User::factory()->create(['password' => 'my-secret-password']);

    // Assert: Password should not be stored as plain text
    expect($user->password)->not->toBe('my-secret-password');

    // Assert: Check if hash is valid
    expect(Hash::check('my-secret-password', $user->password))->toBeTrue();
});

it('hides sensitive attributes when converted to array', function () {
    $user = User::factory()->create();

    $array = $user->toArray();

    // Assert: Hidden fields (defined in #[Hidden]) should be missing
    expect($array)->not->toHaveKey('password')
        ->and($array)->not->toHaveKey('remember_token');
});

/**
 * Role Logic
 */

it('correctly identifies admin and regular users', function () {
    $admin = User::factory()->admin()->create();
    $regular = User::factory()->create(['role' => 'user']);

    expect($admin->isAdmin())->toBeTrue()
        ->and($regular->isAdmin())->toBeFalse();
});

/**
 * Relationships
 */

/* with RecipeFactory
it('can have multiple recipes associated with it', function () {
    $user = User::factory()->create();

    // Action: Create recipes for this user
    // Note: This requires Recipe factory to be ready later
    Recipe::factory()->count(3)->create(['user_id' => $user->id]);

    // Assert: Relationship returns correct count
    expect($user->recipes)->toHaveCount(3)
        ->and($user->recipes->first())->toBeInstanceOf(Recipe::class);
});
*/
