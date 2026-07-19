<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $table = 'marketing_forms';

    protected $fillable = [
        'user_id', 'name', 'path',
    ];
}
