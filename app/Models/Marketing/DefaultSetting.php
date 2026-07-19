<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultSetting extends Model
{
    use HasFactory;

    protected $table = 'default_settings';

    protected $fillable = [
        'user_id', 'timezone', 'delay_time', 'time_format', 'date_format',
        'image_url_hide', 'disable_notification', 'default_from_name',
        'default_from_email', 'default_header', 'default_footer', 'default_reply_to',
    ];
}
