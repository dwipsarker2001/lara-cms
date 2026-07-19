<?php

namespace App\Models;

use Database\Factories\CollectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collection extends Model
{
    /** @use HasFactory<CollectionFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'show_in_menu', 'enable_seo', 'description', 'fields', 'position'];

    public function entries(): HasMany
    {
        return $this->hasMany(CollectionEntry::class);
    }

    public function getTitleAttribute(): string
    {
        return $this->name;
    }

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'enable_seo' => 'boolean',
        ];
    }
}
