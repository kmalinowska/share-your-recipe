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
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile & Favourites (with auth)
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Favourites
    Route::get('/favourites', [FavouriteController::class, 'index'])->name('favourites.index');
    Route::post('/recipes/{recipe}/favourite', [FavouriteController::class, 'toggle'])->name('favourites.toggle');
});

// Recipes
Route::resource('recipes', RecipeController::class);
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/{recipe:slug}', [RecipeController::class, 'show'])->name('recipes.show');
Route::get('/recipes/tags/{tag:slug}', [RecipeController::class, 'tagIndex'])->name('recipes.tag');
Route::get('/category/{category:slug}', [RecipeController::class, 'indexByCategory'])->name('recipes.category');

// Comments
Route::post('/recipes/{recipe}/comments', [CommentController::class, 'store'])->name('comments.store');

// Auth routes (login, register, logout)
require __DIR__.'/auth.php';
