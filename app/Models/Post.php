<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tags' => 'array',
        'sections' => 'array',
        'date' => 'date',
        'published' => 'boolean',
    ];
}
