<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportPreference extends Model
{
    use HasFactory;

    protected $table = 'report_preferences';

    protected $fillable = [
        'id', 'user_id', 'report_enabled', 'last_report_sent_at',
    ];
}
