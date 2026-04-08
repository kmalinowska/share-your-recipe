<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
class Tag extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = ['name', 'slug'];

    protected static function booted():void {
        static::creating(function ($tag) {
           $tag->slug = Str::slug($tag->name);
        });
    }

    //relations
    // Tag can be assigned to multiple recipes
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_tag');
    }

}
