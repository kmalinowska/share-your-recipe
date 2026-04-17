<?php

use App\Models\Favourite;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user and a recipe', function () {
    $favourite = Favourite::factory()->create();

    expect($favourite->user)->toBeInstanceOf(User::class)
        ->and($favourite->recipe)->toBeInstanceOf(Recipe::class);
});

it('prevents duplicate favourites for the same user and recipe', function () {
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create();

    Favourite::factory()->create(['user_id' => $user->id, 'recipe_id' => $recipe->id]);

    // Attempting to create a second identical record should result in an SQL exception
    expect(fn() => Favourite::factory()->create(['user_id' => $user->id, 'recipe_id' => $recipe->id]))
        ->toThrow(Illuminate\Database\QueryException::class);
});
