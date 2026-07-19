<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'campaigns';

    protected $fillable = [
        'user_id', 'name', 'from_email', 'from_name', 'reply_to', 'name_to',
        'receiver_emails', 'subject_line', 'preview_text', 'template_id',
        'active_google_analytics', 'embed_images', 'add_tag', 'add_attachment',
        'custom_unsubscribe', 'update_profile_form', 'enable_mirror',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    public function stats(): HasMany
    {
        return $this->hasMany(Stats::class, 'camp_id', 'id');
    }

    public function schedule(): HasOne
    {
        return $this->hasOne(Schedule::class, 'camp_id', 'id');
    }
}
