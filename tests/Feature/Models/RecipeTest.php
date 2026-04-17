<?php

use App\Models\Recipe;
use App\Models\User;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('generates a valid uuid on creation', function () {
    $recipe = Recipe::factory()->create();
    expect($recipe->id)->toBeString()->toHaveLength(36);
});

it('automatically generates a unique slug from title', function () {
    $title = 'Delicious Apple Pie';

    $recipe1 = Recipe::factory()->create(['title' => $title]);
    $recipe2 = Recipe::factory()->create(['title' => $title]);

    expect($recipe1->slug)->toBe('delicious-apple-pie')
        ->and($recipe2->slug)->toBe('delicious-apple-pie-1');
});

it('belongs to a user and a category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $recipe = Recipe::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    expect($recipe->user)->toBeInstanceOf(User::class)
        ->and($recipe->category)->toBeInstanceOf(Category::class)
        ->and($recipe->user->id)->toBe($user->id);
});

it('can have many ingredients with pivot data', function () {
    $recipe = Recipe::factory()->create();
    $ingredient = Ingredient::factory()->create();

    $recipe->ingredients()->attach($ingredient->id, [
        'quantity' => '500',
        'unit' => 'g'
    ]);

    expect($recipe->ingredients)->toHaveCount(1)
        ->and($recipe->ingredients->first()->pivot->quantity)->toBe('500')
        ->and($recipe->ingredients->first()->pivot->unit)->toBe('g');
});

it('can have many tags', function () {
    $recipe = Recipe::factory()->create();
    $tags = Tag::factory()->count(3)->create();

    $recipe->tags()->attach($tags->pluck('id'));

    expect($recipe->tags)->toHaveCount(3);
});
