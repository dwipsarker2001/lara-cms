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

    /** Route path for this page ("/" for home, "/{slug}" otherwise). */
    public function route(): string
    {
        return $this->slug === 'home' ? '/' : '/'.$this->slug;
    }
}
