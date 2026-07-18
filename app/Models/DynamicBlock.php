<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicBlock extends Model
{
    protected $fillable = ['name', 'label', 'global', 'background', 'fields', 'template'];

    public function getTitleAttribute(): string
    {
        return $this->label;
    }

    protected function casts(): array
    {
        return [
            'global' => 'boolean',
            'background' => 'boolean',
            'fields' => 'array',
        ];
    }
}
