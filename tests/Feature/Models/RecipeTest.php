<?php

use App\Models\Recipe;
use App\Models\User;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\CategorySeeder;

uses(RefreshDatabase::class);
beforeEach(function(){
    $this->seed(CategorySeeder::class);
});

it('generates a valid uuid on creation', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);
    expect($recipe->id)->toBeString()->toHaveLength(36);
});

it('automatically generates a unique slug from title', function () {
    $category = Category::first();
    $title = 'Delicious Apple Pie';

    $recipe1 = Recipe::factory()->create(['title' => $title, 'category_id' => $category->id]);
    $recipe2 = Recipe::factory()->create(['title' => $title, 'category_id' => $category->id]);

    expect($recipe1->slug)->toBe('delicious-apple-pie')
        ->and($recipe2->slug)->toBe('delicious-apple-pie-1');
});

it('belongs to a user and a category', function () {
    $category = Category::first();
    $user = User::factory()->create();

    $recipe = Recipe::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    expect($recipe->user)->toBeInstanceOf(User::class)
        ->and($recipe->category)->toBeInstanceOf(Category::class)
        ->and($recipe->user->id)->toBe($user->id);
});

it('can have many ingredients with pivot data', function () {
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);
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
    $category = Category::first();
    $recipe = Recipe::factory()->create(['category_id' => $category->id]);
    $tags = Tag::factory()->count(3)->create();

    $recipe->tags()->attach($tags->pluck('id'));

    expect($recipe->tags)->toHaveCount(3);
});

it('casts is_commentable to boolean', function () {
    $category = Category::first();

    $recipe = Recipe::factory()->create(['category_id' => $category->id, 'is_commentable' => true]);
    expect($recipe->is_commentable)->toBeBool()
        ->and($recipe->is_commentable)->toBeTrue();

    // Test for exact projection of values from the database (0 and 1 to false and true)
    $recipe->update(['is_commentable' => 0]);
    expect($recipe->fresh()->is_commentable)->toBeFalse();

    $recipe->update(['is_commentable' => 1]);
    expect($recipe->fresh()->is_commentable)->toBeTrue();
});
