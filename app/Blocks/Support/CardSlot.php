<?php

namespace App\Blocks\Support;

/**
 * CardSlot
 *
 * Defines a single mappable slot on a list block's card (e.g. Title, Image, Price).
 * Each slot has a unique key, a human label, and a type hint that tells the mapping
 * UI which kind of field it expects.
 *
 * Types: 'text' | 'image' | 'price' | 'url'
 */
class CardSlot
{
    public function __construct(
        /** Internal key used in $data mapping and Blade templates, e.g. 'image'. */
        public readonly string $key,

        /** Human-readable label shown in the block sidebar, e.g. 'Card Thumbnail'. */
        public readonly string $label,

        /** Slot type hint: 'text' | 'image' | 'price' | 'url'. */
        public readonly string $type = 'text',
    ) {}

    /** A plain text card slot (title, description, badge, etc.). */
    public static function text(string $key, string $label): self
    {
        return new self($key, $label, 'text');
    }

    /** An image URL card slot. */
    public static function image(string $key, string $label): self
    {
        return new self($key, $label, 'image');
    }

    /** A numeric/currency card slot. */
    public static function price(string $key, string $label): self
    {
        return new self($key, $label, 'price');
    }

    /** A URL/link card slot. */
    public static function url(string $key, string $label): self
    {
        return new self($key, $label, 'url');
    }
}
