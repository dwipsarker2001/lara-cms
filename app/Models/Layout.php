<?php

namespace App\Models;

use Database\Factories\LayoutFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
    /** @use HasFactory<LayoutFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'sections' => 'array',
    ];

    public function getTitleAttribute(): string
    {
        return $this->name;
    }
}
