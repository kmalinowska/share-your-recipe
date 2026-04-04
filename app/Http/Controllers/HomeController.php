<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {

        // Categories for the "Choose Category" section tiles
        $categories = Category::orderBy('name')->get();

        // Download the 6 newest recipes with reference to the author and category
        $latestRecipes = Recipe::with(['user', 'category', 'tags'])
            ->latest()
            ->limit(6)
            ->get();

        // 10 last comments from all recipes
        $recentComments = Comment::with(['user', 'recipe', 'parent.user'])
//            ->whereNull('parent_id') // only main, without answers
            ->latest()
            ->limit(10)
            ->get();

        return view('home', compact('categories', 'latestRecipes', 'recentComments'));
    }
}
