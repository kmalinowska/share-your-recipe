<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Ingredient extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;
    protected $fillable = ['name', 'slug'];

    protected static function booted():void{
        static::creating(function ($ingredient) {
            $ingredient->slug = Str::slug($ingredient->name);
        });
    }

    //relations
    //ingredient can be in many recipes
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredients')
            ->withPivot('quantity', 'unit');
    }
}
