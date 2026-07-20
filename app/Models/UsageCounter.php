<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'emails_sent_this_cycle',
        'contacts_count',
        'campaigns_count',
        'groups_count',
        'cycle_started_at',
    ];

    protected function casts(): array
    {
        return [
            'cycle_started_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

