<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'recipe_id',
    'user_id',
    'guest_name',
    'content',
    'parent_id'
])]

class Comment extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = true;
    const UPDATED_AT = null;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    //relations
    //comment belongs to recipe
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    // The comment can belong to the user (or be from a guest - then null)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //comment can have many replies
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('id');
    }

    //comment belongs to parent
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    //accessor for author_name
    public function getAuthorNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Anonim';
    }
}
