<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\FavouriteController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile (with auth)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Recipes
Route::resource('recipes', RecipeController::class);
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('/recipes/{recipe:slug}', [RecipeController::class, 'show'])->name('recipes.show');
Route::get('/recipes/tags/{tag:slug}', [RecipeController::class, 'tagIndex'])->name('recipes.tag');
Route::get('/category/{category:slug}', [RecipeController::class, 'indexByCategory'])->name('recipes.category');

// Favourites
Route::get('favourites', [FavouriteController::class, 'index'])
    ->middleware('auth')->name('favourites.index');
Route::post('/recipes/{recipe}/favourite', [FavouriteController::class, 'toggle'])
    ->middleware('auth')
    ->name('favourites.toggle');

// Auth routes (login, register, logout)
require __DIR__.'/auth.php';
