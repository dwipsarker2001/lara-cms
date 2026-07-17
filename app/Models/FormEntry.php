<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormEntry extends Model
{
    protected $fillable = ['form_id', 'data'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
