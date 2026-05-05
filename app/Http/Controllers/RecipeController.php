<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\View\View;
use Illuminate\Http\Request;

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

        // download favourite IDs
        $userFavourites = auth()->check()
            ? auth()->user()->favourites()->pluck('recipe_id')->toArray()
            : [];

        return view('recipes.show', compact('recipe', 'comments', 'userFavourites'));
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
}
