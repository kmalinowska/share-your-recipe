<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Tag;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\RecipeStoreRequest;
use App\Http\Requests\GenerateRecipeTagsRequest;
use App\Services\AI\RecipeTagSuggestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecipeController extends Controller
{
    // Displays all recipes (newest first)
    public function index(Request $request): View {
        $categories = Category::all();
        $recipes = Recipe::with(['user', 'category', 'tags'])
            ->search([
                'search' => $request->search,
                'categories' => $request->categories
            ])
            ->latest()
            ->paginate(4)
            ->withQueryString();

        return view('recipes.index', [
            'recipes' => $recipes,
            'categories' => $categories,
            'userFavourites' => $this->getUserFavourites(),
            'selectedCategories' => $request->categories ?? []
        ]);
    }

    // Displays details of a specific recipe
    public function show(Recipe $recipe): View
    {
        // load the basic relations
        $recipe->load([
            'user',
            'category',
            'ingredients',
            'tags'
        ]);

        // Load only root comments (parent_id = null) with their direct replies.
        // Flat threading: replies are always at depth = 1, never deeper.
        // We only need one level of replies.user because there is no deeper nesting
        $comments = $recipe->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user']) // one level of replies is enough — flat threading
            ->latest()
            ->paginate(10);

        $totalCommentsCount = $recipe->allComments()->count();
        $threadCount        = $recipe->comments()->count();

        // download favourite IDs
        $userFavourites = auth()->check()
            ? auth()->user()->favourites()->pluck('recipe_id')->toArray()
            : [];

        return view('recipes.show', compact('recipe', 'comments', 'totalCommentsCount', 'threadCount', 'userFavourites'));
    }

    // Show the form for creating a new recipe
    public function create(): View {
        // download categories alphabetically
        $categories = Category::orderBy('name')->get();

        // get the tags alphabetically so they look nice in the form
        $tags = Tag::orderBy('name')->get();

        return view('recipes.create', compact('categories', 'tags'));
    }

    /**
     * GenerateTags with AI
     */
    public function generateTags(
        GenerateRecipeTagsRequest $request,
        RecipeTagSuggestionService $ai
    ) {
        $validated = $request->validated();

        $category = Category::findOrFail(
          $validated['category_id']
        );

        $availableTags = Tag::orderBy('name')
            ->pluck('name')
            ->all();

        $suggestedTags = $ai->suggest(
            title: $validated['title'],
            category: $category->name,
            ingredients: $validated['ingredients'],
            steps: $validated['steps'],
            availableTags: $availableTags,
        );

        $tagIds = Tag::whereIn('name', $suggestedTags)
            ->pluck('id');

        return response()->json([
            'tags' => $tagIds,
        ]);
    }

    /**
     * Store a newly created recipe in storage.
     */
    public function store(RecipeStoreRequest $request): RedirectResponse
    {
        // 1. Download fully verified data from Request
        $validated = $request->validated();

        // 2. Handling a photo (if uploaded)
        $imagePath = null;
        if ($request->hasFile('image_path') && $request->file('image_path')->isValid()) {
            $imagePath = $request->file('image_path')->store('recipe', 'public');
        }

        // 3. Create a recipe object in the database assigned to the logged in user
        $recipe = Recipe::create([
            'title' => $validated['title'],
            'preparation'      => $validated['steps'],
            'preparation_time' => $validated['preparation_time'],
            'category_id' => $validated['category_id'],
            'user_id' => $request->user()->id, // Safe beating from the session, not from the input!
            'image_path' => $imagePath,
        ]);

        // 4. Dynamic Component Association (Intermediate Table)
        foreach ($validated['ingredients'] as $ingData) {
            $ingredient = Ingredient::firstOrCreate(
                ['name' => $ingData['name']],
                ['slug' => Str::slug($ingData['name'])]
            );

            $recipe->ingredients()->attach($ingredient->id, [
                'quantity' => $ingData['quantity'],
                'unit' => $ingData['unit']
            ]);
        }

        // 5. Tag association (if any selected)
        if (!empty($validated['tags'])) {
            $recipe->tags()->attach($validated['tags']);
        }

        // 6. Redirection successful
        return redirect()->route('recipes.show', $recipe)->with('success', 'Recipe created successfully!');
    }

    // Display recipes by tags
    public function tagIndex(Tag $tag): View
    {
        $recipes = $tag->recipes()
            ->with(['category', 'user'])
            ->latest()
            ->paginate(12);

        $userFavourites = auth()->check()
            ? auth()->user()->favourites()->pluck('recipe_id')->toArray()
            : [];

        return view('recipes.index', [
            'recipes' => $recipes,
            'title' => "Recipes with tag: #{$tag->name}",
            'userFavourites' => $this->getUserFavourites(),
            'activeTag' => $tag->slug
        ]);
    }

    // Displays recipes from a specific category (newest first)
    public function indexByCategory(Category $category): View
    {
        $recipes = $category->recipes()
            ->with(['user', 'category', 'tags'])
            ->latest()
            ->paginate(12);

        return view('recipes.index', [
            'recipes' => $recipes,
            'currentCategory' => $category,
            'userFavourites' => $this->getUserFavourites()
        ]);
    }

    // retrieve the ID of favorite recipes only for the logged in user
    private function getUserFavourites(): array {
        return auth()->check()
            ? auth()->user()->favourites()->pluck('recipe_id')->toArray()
            : [];
    }
    public function edit(Recipe $recipe)
    {
        // Authorization: only the recipe owner can edit their recipe
        if (auth()->id() !== $recipe->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Load data required by the edit form
        $categories = Category::all();
        $tags = Tag::all();

        // Display the recipe edit form
        return view('recipes.edit', compact('recipe', 'categories', 'tags'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        // Authorization: only the recipe owner can update their recipe
        if (auth()->id() !== $recipe->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Validation rules
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'preparation_time' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.quantity' => 'required|string|max:50',
            'ingredients.*.unit' => 'required|string|max:50',
            'steps' => 'required|array|min:1',
            'steps.*' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Handle Image Upload if a new one is provided
        if ($request->hasFile('image_path')) {
            // Delete old image from storage if exists
            if ($recipe->image_path) {
                Storage::disk('public')->delete($recipe->image_path);
            }
            $validated['image_path'] = $request->file('image_path')->store('recipe', 'public');
        }

        // Handle the comment section toggle (checkbox behavior)
        $validated['is_commentable'] = $request->has('is_commentable');

        // Update main recipe fields and preparation json array
        $recipe->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'preparation_time' => $validated['preparation_time'],
            'category_id' => $validated['category_id'],
            'preparation' => $validated['steps'], // Maps steps array into JSON
            'image_path' => $validated['image_path'] ?? $recipe->image_path,
            'is_commentable' => $validated['is_commentable'],
        ]);

        // Sync Ingredients (Pivot table mapping)
        $ingredientData = [];
        foreach ($validated['ingredients'] as $item) {
            // Find or create ingredient to get its global ID
            $ingredient = Ingredient::firstOrCreate(['name' => Str::lower($item['name'])]);

            $ingredientData[$ingredient->id] = [
                'quantity' => $item['quantity'],
                'unit' => Str::lower($item['unit']),
            ];
        }
        // sync() automatically removes old relations and attaches new ones
        $recipe->ingredients()->sync($ingredientData);

        // Sync Tags
        $recipe->tags()->sync($validated['tags'] ?? []);

        // Save all changes and redirect back to the profile page
        return redirect()
            ->route('profile.edit')
            ->with('success', 'Recipe updated successfully!');
    }

    public function destroy(Recipe $recipe)
    {
        // Authorization: only the recipe owner can delete their recipe
        if (auth()->id() !== $recipe->user_id) {
            abort(403);
        }

        // Remove recipe image from storage if it exists
        if ($recipe->image_path) {
            Storage::disk('public')->delete($recipe->image_path);
        }

        // Delete the recipe record
        $recipe->delete();

        // Redirect back with success message
        return redirect()
            ->route('profile.edit')
            ->with('success', 'Recipe deleted successfully.');
    }
}
