<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = [
        'group_id', 'user_id', 'email', 'firstname', 'lastname', 'sms',
        'whatsapp', 'double_opt_in', 'opt_in', 'is_unsubscribed', 'unsubscribed_at',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
