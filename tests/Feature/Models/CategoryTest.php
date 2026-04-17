<?php

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

uses(RefreshDatabase::class);

it('creates a category with a valid uuid', function() {
    // Action: create a category by factory
    $category = Category::factory()->create();
    // Check: Whether the ID is not empty and whether it is a string (UUID)
    expect($category->id)->not->toBeNull()
        ->and($category->id)->toBeString()
        ->and(strlen($category->id))->toBeGreaterThan(30);
});

it('generates a slug automatically from the name', function() {
    // 1. Action: create a category with a specific name
    $category = Category::factory()->create([
        'name' => 'Delicious Breakfasts'
    ]);
    // 2. Check: Whether the slug was created automatically according to the logic in the model
    expect($category->slug)->toBe('delicious-breakfasts');
});

it('has a recipes relationship', function() {
    $category = Category::factory()->create();
    // Check if the relation returns a collection (even if empty)
    expect($category->recipes)->toBeInstanceOf(Collection::class);
});

it('does not have timestamps', function() {
   $category = Category::factory()->create();
   // Check if the fields created_at and updated_at do not exist in the object
    expect(isset($category->created_at))->toBeFalse()
        ->and(isset($category->updated_at))->toBeFalse();
});

it('has correct fillable attributes', function() {
   $category = new Category();
   // Check if the fillable table is exactly as expected
    expect($category->getFillable())->toBe(['name', 'slug']);
});

it('can have many recipes linked to it', function() {
   $category = Category::factory()->create();
   // Create 3 recipes assigned to this category - use RecipeFactory
    Recipe::factory()->count(3)->create([
       'category_id' => $category->id
    ]);
    expect($category->recipes)->toHaveCount(3)
        ->and($category->recipes->first())->toBeInstanceOf(Recipe::class);
});
