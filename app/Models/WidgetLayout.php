<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetLayout extends Model
{
    protected $fillable = ['admin_id', 'layout'];

    protected $casts = [
        'layout' => 'array',
    ];
}
