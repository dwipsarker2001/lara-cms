<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Page
 * A public page. Its content is an ordered list of sections (blocks) stored in
 * the `sections` JSON column: [{ _key, name, data }].
 */
class Page extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sections' => 'array',
        'meta' => 'array',
        'published' => 'boolean',
    ];

    public function collectionEntry()
    {
        return $this->hasOne(CollectionEntry::class);
    }

    /** Route path for this page ("/" for home, "/{slug}" otherwise, or "/{collectionSlug}/{slug}" if it belongs to a collection entry). */
    public function route(): string
    {
        if ($this->slug === 'home') {
            return '/';
        }

        $this->loadMissing('collectionEntry.collection');
        if ($this->collectionEntry && $this->collectionEntry->collection) {
            return '/'.$this->collectionEntry->collection->slug.'/'.$this->slug;
        }

        return '/'.$this->slug;
    }
}
