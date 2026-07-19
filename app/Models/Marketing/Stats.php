<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stats extends Model
{
    use HasFactory;

    protected $table = 'stats';

    protected $fillable = [
        'id', 'user_id', 'camp_id', 'opened', 'clicked', 'bounced',
        'black_list', 'total_sent', 'created_at', 'updated_at',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'camp_id', 'id');
    }
}
