<?php

namespace App\Models;

use Database\Factories\FormEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormEntry extends Model
{
    /** @use HasFactory<FormEntryFactory> */
    use HasFactory;

    protected $fillable = ['form_id', 'data', 'ip_address', 'user_agent', 'status'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'status' => 'integer',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
