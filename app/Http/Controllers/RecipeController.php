<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
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
        // load the basic relations
        $recipe->load([
            'user',
            'category',
            'ingredients',
            'tags',
//            'comments.user',
//            'comments.replies.user'
        ]);

        // download main comments as a separate collection with pagination, and load replies as a relation
        $comments = $recipe->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(10);

        // download favourite IDs
        $userFavourites = auth()->check()
            ? auth()->user()->favourites()->pluck('recipe_id')->toArray()
            : [];

        return view('recipes.show', compact('recipe', 'comments', 'userFavourites'));
    }

    // Display recipes by tags
    public function tagIndex(Tag $tag)
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
            'userFavourites' => $userFavourites,
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
