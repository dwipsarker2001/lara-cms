<?php

namespace App\Widgets;

use Illuminate\Support\Str;

abstract class Widget
{
    abstract public function label(): string;

    abstract public function render();

    abstract public static function zone(): string;

    public ?string $image = null;

    public static function type(): string
    {
        return Str::snake(class_basename(static::class));
    }

    public static function make(array $config): static
    {
        return new static(...array_diff_key($config, ['type' => null]));
    }
}
