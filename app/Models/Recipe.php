<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'category_id',
    'title',
    'slug',
    'preparation',
    'preparation_time',
    'image_path'
])]
class Recipe extends Model
{
    use HasUuids, HasFactory;

    protected static function booted(): void {
        static::creating(function (Recipe $recipe):void {
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

    public function scopeSearch($query, array $filters)
    {
        return $query->where(function ($q) use ($filters) {
            // 1. Filters by keyword (if expists)
            if (!empty($filters['search'])) {
                $term = Str::lower($filters['search']);
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(preparation) LIKE ?', ["%{$term}%"])
                        ->orWhereHas('ingredients', fn($i) => $i->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]))
                        ->orWhereHas('tags', fn($t) => $t->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]));
                });
            }

            // 2. Filters by category
            if (!empty($filters['categories'])) {
                $q->whereIn('category_id', $filters['categories']);
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

    //recipe has many comments (with replies)
    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
