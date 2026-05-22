<?php

use App\Models\Category;
use App\Models\Favourite;
use App\Models\Recipe;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;

uses(RefreshDatabase::class);
beforeEach(function(){
    $this->seed(CategorySeeder::class);
});

it('belongs to a user and a recipe', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);
    $favourite = Favourite::factory()->create(['recipe_id' => $recipe->id]);

    expect($favourite->user)->toBeInstanceOf(User::class)
        ->and($favourite->recipe)->toBeInstanceOf(Recipe::class);
});

it('prevents duplicate favourites for the same user and recipe', function () {
    $category = Category::first();
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);

    Favourite::factory()->create(['user_id' => $user->id, 'recipe_id' => $recipe->id]);

    expect(fn() => Favourite::factory()->create(['user_id' => $user->id, 'recipe_id' => $recipe->id]))
        ->toThrow(QueryException::class);
});
