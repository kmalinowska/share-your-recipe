<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favourite extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;
    protected $fillable = ['user_id', 'recipe_id'];

    //relations
    //favourite belongs to user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Favorite indicates a recipe
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
