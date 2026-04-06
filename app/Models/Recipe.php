<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Recipe extends Model
{
    use HasUuids, HasFactory;
    protected $fillable = [
      'user_id',
      'category_id',
      'title',
      'slug',
      'preparation',
      'preparation_time',
      'image_path'
    ];

    protected static function booted(): void {
        static::creating(function (Recipe $recipe) {
            if(empty($recipe->slug)) {
                $slug = Str::slug($recipe->title);
                $originalSlug = $slug;
                $count = 1;

                // Check if slug is existing already
                while(static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                $recipe->slug = $slug;
            }
        });
    }

    //relations
    //recipe belongs to user
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
    //recipe belongs to category
    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }
    //recipe has many ingredients (by pivot recipe_ingredients)
    public function ingredients(): BelongsToMany {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot('quantity', 'unit');
    }
    //recipe has many tags (by pivot recipe_tag)
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'recipe_tag');
    }

    //recipe has many favourites
    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class);
    }

    //recipe has many comments (only main, without answers)
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }
}
