<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_directory' => 'boolean',
        ];
    }
}
