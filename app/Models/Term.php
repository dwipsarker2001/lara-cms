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
            'data' => 'array',
            'position' => 'integer',
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

    /**
     * Generate the frontend route or target URL for this term based on custom data, taxonomy pattern, or default path.
     */
    public function route(?string $patternOverride = null): string
    {
        if (! empty($this->data['custom_url'])) {
            return $this->data['custom_url'];
        }

        $this->loadMissing('taxonomy');
        $pattern = $this->taxonomy->route_pattern ?? '';
        if (empty($pattern)) {
            $pattern = $patternOverride;
        }

        if (! empty($pattern)) {
            return str_replace(
                ['{slug}', '{id}', '{title}'],
                [$this->slug, (string) $this->id, urlencode($this->title)],
                $pattern
            );
        }

        $taxSlug = $this->taxonomy->slug ?? 'destinations';

        return '/'.$taxSlug.'/'.$this->slug;
    }
}
