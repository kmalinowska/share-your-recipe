<?php

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;

uses(RefreshDatabase::class);

/**
 * Model Logic Tests
 */

it('automatically generates uuid and slug from name on creation', function () {
    // Act: Create an ingredient providing only the name
    $ingredient = Ingredient::create(['name' => 'Fresh Garlic']);

    // Assert: UUID should be a 36-character string
    expect($ingredient->id)->not->toBeNull()
        ->and($ingredient->id)->toBeString()
        ->toHaveLength(36);

    // Assert: Slug should be automatically generated
    expect($ingredient->slug)->toBe('fresh-garlic');
});

it('creates an ingredient using factory with valid uuid', function () {
    $ingredient = Ingredient::factory()->create();

    expect($ingredient->id)->toBeString()->toHaveLength(36)
        ->and($ingredient->name)->not->toBeEmpty();
});

it('has correct fillable properties', function () {
    $ingredient = new Ingredient();
    expect($ingredient->getFillable())->toBe(['name', 'slug']);
});

it('does not use timestamps', function () {
    $ingredient = Ingredient::factory()->create();
    // Laravel should not include created_at/updated_at fields
    expect(isset($ingredient->created_at))->toBeFalse();
});

/**
 * Database Constraints Tests
 */

it('prevents creating ingredients with duplicate names', function () {
    Ingredient::factory()->create(['name' => 'Sugar']);

    // Attempting to create a duplicate should throw a QueryException
    $createDuplicate = fn() => Ingredient::factory()->create(['name' => 'Sugar']);

    expect($createDuplicate)->toThrow(QueryException::class);
});

/**
 * Relationship and Pivot Table Tests
 */

it('saves quantity and unit in the pivot table correctly', function () {
    // 1. Preparation
    $recipe = Recipe::factory()->create();
    $ingredient = Ingredient::factory()->create(['name' => 'Wheat Flour']);

    // 2. Action: Attach ingredient with pivot data
    $recipe->ingredients()->attach($ingredient->id, [
        'quantity' => '500',
        'unit' => 'g'
    ]);

    // 3. Assert: Check database record
    $this->assertDatabaseHas('recipe_ingredients', [
        'ingredient_id' => $ingredient->id,
        'recipe_id'     => $recipe->id,
        'quantity'      => '500',
        'unit'          => 'g'
    ]);

    // 4. Assert: Check model accessibility via refresh
    $pivotData = $recipe->refresh()->ingredients->first()->pivot;
    expect($pivotData->quantity)->toBe('500')
        ->and($pivotData->unit)->toBe('g');
});

it('prevents adding the same ingredient to a recipe twice', function () {
    $recipe = Recipe::factory()->create();
    $ingredient = Ingredient::factory()->create();

    $recipe->ingredients()->attach($ingredient->id, ['quantity' => '10']);

    // Attaching the same ID again should trigger a unique constraint violation
    $attachAgain = fn() => $recipe->ingredients()->attach($ingredient->id, ['quantity' => '20']);

    expect($attachAgain)->toThrow(QueryException::class);
});

/**
 * Integrity Tests (Delete Protection)
 */

it('protects ingredient from deletion if used in a recipe', function () {
    $recipe = Recipe::factory()->create();
    $ingredient = Ingredient::factory()->create();
    $recipe->ingredients()->attach($ingredient->id, ['quantity' => '1']);

    // Action & Assert: Deleting a used ingredient should fail due to restrictOnDelete()
    expect(fn() => $ingredient->delete())->toThrow(QueryException::class);
});

it('removes pivot records when a recipe is deleted but keeps the ingredient', function () {
    $recipe = Recipe::factory()->create();
    $ingredient = Ingredient::factory()->create();
    $recipe->ingredients()->attach($ingredient->id, ['quantity' => '5']);

    // Action: Delete the recipe
    $recipe->delete();

    // Assert: Pivot record is gone (cascadeOnDelete)
    $this->assertDatabaseMissing('recipe_ingredients', [
        'recipe_id' => $recipe->id,
        'ingredient_id' => $ingredient->id
    ]);

    // Assert: Ingredient dictionary record still exists
    $this->assertDatabaseHas('ingredients', ['id' => $ingredient->id]);
});
