<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionEntry extends Model
{
    protected $fillable = ['collection_id', 'data', 'sections', 'position', 'slug', 'published', 'meta'];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function getTitleAttribute(): string
    {
        if (trim($this->data['title'] ?? '') !== '') {
            return $this->data['title'];
        }
        if (trim($this->data['name'] ?? '') !== '') {
            return $this->data['name'];
        }
        return 'Entry #'.$this->id;
    }

    public function route(): string
    {
        if ($this->slug === 'home') {
            return '/';
        }

        $this->loadMissing('collection');
        if ($this->collection && $this->collection->slug && $this->collection->slug !== 'pages') {
            return '/'.$this->collection->slug.'/'.$this->slug;
        }

        return '/'.$this->slug ?? '#';
    }

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'sections' => 'array',
            'meta' => 'array',
            'published' => 'boolean',
        ];
    }
}
