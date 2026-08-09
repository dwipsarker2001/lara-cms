<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxonomy extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'position' => 'integer',
        ];
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class)->orderBy('position')->orderBy('title');
    }
}
