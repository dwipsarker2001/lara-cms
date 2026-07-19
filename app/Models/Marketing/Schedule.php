<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $fillable = [
        'id', 'camp_id', 'schedule_date', 'schedule_time', 'status',
        'created_at', 'updated_at',
    ];
}
