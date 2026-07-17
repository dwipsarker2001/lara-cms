<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionEntry extends Model
{
    protected $fillable = ['collection_id', 'data', 'sections', 'position', 'page_id'];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getTitleAttribute(): string
    {
        return trim($this->data['title'] ?? '') !== ''
            ? $this->data['title']
            : 'Entry #'.$this->id;
    }

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'sections' => 'array',
        ];
    }
}
