<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Term extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'taxonomy_id' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Term $term) {
            if (empty($term->slug)) {
                $term->slug = str($term->title)->slug()->limit(255)->toString();
            }
        });

        static::updating(function (Term $term) {
            if ($term->isDirty('title') && ! $term->isDirty('slug')) {
                $term->slug = str($term->title)->slug()->limit(255)->toString();
            }
        });
    }

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }
}
