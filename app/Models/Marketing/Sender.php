<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sender extends Model
{
    use HasFactory;

    protected $table = 'senders';

    protected $fillable = [
        'user_id', 'domain', 'sendgrid_id', 'created_at', 'updated_at',
    ];
}
