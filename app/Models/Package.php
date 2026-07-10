<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = [];

    protected $casts = [
        'blocks' => 'array',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'published' => 'boolean',
    ];
}
