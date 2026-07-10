<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'guests' => 'integer',
        'amount' => 'decimal:2',
    ];
}
