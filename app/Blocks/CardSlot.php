<?php

namespace App\Blocks;

/**
 * CardSlot
 *
 * Defines a single mappable slot on a list block's card (e.g. Title, Image, Price).
 * Types: 'text' | 'image' | 'price' | 'url'
 */
class CardSlot
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly string $type = 'text',
    ) {}

    public static function text(string $key, string $label): self
    {
        return new self($key, $label, 'text');
    }

    public static function image(string $key, string $label): self
    {
        return new self($key, $label, 'image');
    }

    public static function price(string $key, string $label): self
    {
        return new self($key, $label, 'price');
    }

    public static function url(string $key, string $label): self
    {
        return new self($key, $label, 'url');
    }
}
