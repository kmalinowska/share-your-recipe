<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Favourite;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    // show logged user favourite list
    public function index() {
        $favourites = Auth::user()->favourites()
            ->with(['recipe.user', 'recipe.category', 'recipe.tags'])
            ->latest()
            ->paginate(12);

        $recipes = $favourites->getCollection()->map(fn($f) => $f->recipe);

        return view('favourites.index', [
           'recipes' => $recipes,
           'favourites_paginated' => $favourites,
           'userFavourites' => Auth::user()->favourites()->pluck('recipe_id')->toArray()
        ]);
    }

    // Action: click on heart icon
    public function toggle(Recipe $recipe) {
        $userId = Auth::id();
        $favourite = Favourite::where('user_id', $userId)
            ->where('recipe_id', $recipe->id)
            ->first();

        if ($favourite) {
            $favourite->delete();
            $message = 'Removed from favourites.';
        } else {
            Favourite::create([
                'user_id' => $userId,
                'recipe_id' => $recipe->id
            ]);
            $message = 'Added to favourites!';
        }

        return back()->with('success', $message);
    }
}
