<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $table = 'templates';

    protected $fillable = [
        'user_id', 'name', 'template_id', 'published', 'content',
    ];

    public function getTitleAttribute(): string
    {
        return $this->name;
    }
}
