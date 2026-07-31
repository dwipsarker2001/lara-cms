<?php

namespace App\Models;

use Database\Factories\FormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    /** @use HasFactory<FormFactory> */
    use HasFactory;

    protected $fillable = ['title', 'icon', 'description', 'submit_text', 'success_message', 'per_page', 'position', 'fields'];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'per_page' => 'integer',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(FormEntry::class);
    }
}
