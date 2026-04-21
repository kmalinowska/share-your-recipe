<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use Illuminate\View\View;

class RecipeController extends Controller
{
    // Displays all recipes (newest first)
    public function index(): View {
        $recipes = Recipe::with(['user', 'category', 'tags'])
            ->latest()
            ->paginate(4);

        return view('recipes.index', [
            'recipes' => $recipes,
            'userFavourites' => $this->getUserFavourites()
        ]);
    }

    // Displays details of a specific recipe
    public function show(Recipe $recipe): View
    {
        // We load the relations needed for the full recipe view
        $recipe->load([
            'user',
            'category',
            'ingredients',
            'tags',
            'comments.user',
            'comments.replies.user'
        ]);

        return view('recipes.show', compact('recipe'));
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
