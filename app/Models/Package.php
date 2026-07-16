<?php

namespace App\Models;

use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'blocks' => 'array',
        'published' => 'boolean',
    ];

    public function getSectionsAttribute(): array
    {
        return $this->blocks ?? [];
    }

    public function setSectionsAttribute(array $value): void
    {
        $this->blocks = $value;
    }
}
