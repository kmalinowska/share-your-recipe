<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Tag extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false; // tabela tags nie ma timestamps

    protected $fillable = ['name'];

}
