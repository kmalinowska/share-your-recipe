<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Recipe extends Model
{
    use HasUuids;
    protected $fillable = [
      'user_id',
      'category_id',
      'title',
      'slug',
      'preparation',
      'time_preparation',
      'image_path'
    ];
}
