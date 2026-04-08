<?php

use App\Models\Tag;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a tag using factory with valid uuid', function () {
    $tag = Tag::factory()->create();
    expect($tag->id)->toBeString()->toHaveLength(36)
        ->and($tag->name)->not->toBeEmpty(); // check if the factory has drawn anything at all
});

it('automatically generates uuid and slug from name on creation', function(){
   // provide the name only
    $tag = Tag::create(['name' => 'Italian Food']);
    // check UUID
    expect($tag->id)->not->toBeNull()
        ->and($tag->id)->toHaveLength(36);
    // check if slug generated from a name
    expect($tag->slug)->toBe('italian-food');
});

it('can be associated with multiple recipes', function() {
    $tag = Tag::factory()->create(['name' => 'Spicy']);
    $recipes = Recipe::factory()->count(3)->create();

    // combine the tag with 3 recipes
    $tag->recipes()->attach($recipes->pluck('id'));

    expect($tag->recipes)->toHaveCount(3)
        ->and($tag->recipes->first())->toBeInstanceOf(Recipe::class);
});

it('can be assigned to multiple recipes and vice versa', function () {
    // 1. Preparation: 2 tags and 2 recipes
    $tag1 = Tag::factory()->create(['name' => 'Vege']);
    $tag2 = Tag::factory()->create(['name' => 'FastFood']);

    $recipe1 = Recipe::factory()->create();
    $recipe2 = Recipe::factory()->create();

    // 2. Action: attach
    $recipe1->tags()->attach([$tag1->id, $tag2->id]);
    $tag1->recipes()->attach($recipe2->id);

    // 3. Check: Do relationships work both ways?
    expect($recipe1->tags)->toHaveCount(2);
    expect($tag1->recipes)->toHaveCount(2); // Have recipe 1 and recipe 2
    expect($tag2->recipes)->toHaveCount(1); // Have only recipe 1
});

it('detaches tags from recipe correctly', function () {
    $recipe = Recipe::factory()->create();
    $tag = Tag::factory()->create();

    $recipe->tags()->attach($tag->id);
    expect($recipe->tags)->toHaveCount(1);

    $recipe->tags()->detach($tag->id);

    // Refresh the relationship in the model
    expect($recipe->refresh()->tags)->toHaveCount(0);
});

// unique constraint test - checks whether the database will throw an error when you try to create a second tag with the same name
// works only if column name in migration have ->unique()
it('prevents creating tags with duplicate names', function () {
    // 1. Create a first tag
    Tag::factory()->create(['name' => 'Vegan']);

    // 2. Try to create a second tag with the same name
    // check exception, wrapping the action in an anonymous function
    $createDuplicate = fn() => Tag::factory()->create(['name' => 'Vegan']);

    // expect the database to throw a QueryException
    expect($createDuplicate)->toThrow(\Illuminate\Database\QueryException::class);
});

//cascade delete test - check whether, when a recipe is deleted, the records linking it to tags in the recipe_tag table also disappear
it('automatically removes pivot records when a recipe is deleted', function () {
    // 1. Preparation: Recipe linked to tag
    $recipe = Recipe::factory()->create();
    $tag = Tag::factory()->create();
    $recipe->tags()->attach($tag->id);

    // check if the record in the linking table really exists
    $this->assertDatabaseHas('recipe_tag', [
        'recipe_id' => $recipe->id,
        'tag_id'    => $tag->id
    ]);

    // 2. Action: remove a recipe
    $recipe->delete();

    // 3. Check: Has the record in the linking table disappeared?
    $this->assertDatabaseMissing('recipe_tag', [
        'recipe_id' => $recipe->id,
        'tag_id'    => $tag->id
    ]);

    // Make sure that the tag itself STILL EXISTS
    // (we don't want to remove the "Vege" tag just because we removed one recipe)
    expect(Tag::find($tag->id))->not->toBeNull();
});

it('has correct fillable properties', function () {
    $tag = new Tag();
    expect($tag->getFillable())->toBe(['name', 'slug']);
});

it('does not use timestamps', function() {
   $tag = Tag::factory()->create();
   expect(isset($tag->created_at))->toBeFalse();
});
