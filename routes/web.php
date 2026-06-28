<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Dashboard
Route::get('/dashboard', function () {
    return redirect()->route('profile.edit');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile & Favourites  & Create/Manage recipe (with auth)
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Favourites
    Route::get('/favourites', [FavouriteController::class, 'index'])->name('favourites.index');
    Route::post('/recipes/{recipe}/favourite', [FavouriteController::class, 'toggle'])->name('favourites.toggle');

    // Recipes Management
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::post('/recipes/generate-tags', [RecipeController::class, 'generateTags'])->name('recipe.generate-tags');
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    // Comments Management
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Recipes
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/{recipe:slug}', [RecipeController::class, 'show'])->name('recipes.show');
Route::get('/recipes/tags/{tag:slug}', [RecipeController::class, 'tagIndex'])->name('recipes.tag');
Route::get('/category/{category:slug}', [RecipeController::class, 'indexByCategory'])->name('recipes.category');

// Comments
Route::post('/recipes/{recipe}/comments', [CommentController::class, 'store'])->name('comments.store');

// Auth routes (login, register, logout)
require __DIR__.'/auth.php';
