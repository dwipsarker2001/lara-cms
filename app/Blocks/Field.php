<?php

namespace App\Blocks;

/**
 * Field
 * Fluent builders for a block's editable fields. Each returns a plain array
 * (a "FieldDef") consumed by the auto editor and the default-data builder.
 *
 * A field is either a SCALAR (string, number, boolean, datetime, image, icon,
 * rich-text, background, link, tags) or the one composite type OBJECT, which
 * groups child fields and — with list:true — repeats as an array of objects.
 *
 * Developers building sections only ever touch these helpers.
 */
class Field
{
    public static function string(string $name, string $label, string $default = '', bool $multiline = false): array
    {
        return compact('name', 'label') + ['type' => 'string', 'defaultValue' => $default, 'multiline' => $multiline];
    }

    public static function text(string $name, string $label, string $default = ''): array
    {
        return self::string($name, $label, $default, multiline: true);
    }

    public static function number(string $name, string $label, int|float|string $default = ''): array
    {
        return compact('name', 'label') + ['type' => 'number', 'defaultValue' => (string) $default];
    }

    public static function boolean(string $name, string $label, bool $default = false): array
    {
        return compact('name', 'label') + ['type' => 'boolean', 'defaultValue' => $default ? 'true' : 'false'];
    }

    public static function datetime(string $name, string $label, string $default = ''): array
    {
        return compact('name', 'label') + ['type' => 'datetime', 'defaultValue' => $default];
    }

    public static function image(string $name, string $label, string $default = ''): array
    {
        return compact('name', 'label') + ['type' => 'image', 'defaultValue' => $default];
    }

    public static function icon(string $name, string $label, string $default = ''): array
    {
        return compact('name', 'label') + ['type' => 'icon', 'defaultValue' => $default];
    }

    public static function richText(string $name, string $label, string $default = ''): array
    {
        return compact('name', 'label') + ['type' => 'rich-text', 'defaultValue' => $default];
    }

    public static function link(string $name, string $label, string $default = ''): array
    {
        return compact('name', 'label') + ['type' => 'link', 'defaultValue' => $default];
    }

    public static function tags(string $name, string $label): array
    {
        return compact('name', 'label') + ['type' => 'tags', 'defaultValue' => '[]'];
    }

    public static function background(): array
    {
        return [
            'name' => 'background',
            'label' => 'Background',
            'type' => 'object',
            'fields' => [
                self::image('image', 'Background Image'),
                self::select('color', 'Background Color', [
                    ['value' => '#ffffff', 'label' => 'White'],
                    ['value' => '#000000', 'label' => 'Black'],
                    ['value' => '#f3f4f6', 'label' => 'Light Gray'],
                    ['value' => '#e5e7eb', 'label' => 'Gray'],
                    ['value' => '#eff6ff', 'label' => 'Light Blue'],
                    ['value' => '#dbeafe', 'label' => 'Blue'],
                    ['value' => '#f0fdf4', 'label' => 'Light Green'],
                    ['value' => '#dcfce7', 'label' => 'Green'],
                    ['value' => '#fef2f2', 'label' => 'Light Red'],
                    ['value' => '#fefce8', 'label' => 'Light Yellow'],
                    ['value' => '#f5f3ff', 'label' => 'Light Purple'],
                    ['value' => '#fff7ed', 'label' => 'Light Orange'],
                ], default: '#ffffff'),
                self::number('opacity', 'Opacity', default: 100),
            ],
        ];
    }

    public static function select(string $name, string $label, array $options, string $default = ''): array
    {
        return compact('name', 'label', 'options') + ['type' => 'select', 'defaultValue' => $default];
    }

    /**
     * A single nested group of fields.
     *
     * @param  array<int, array>  $fields
     */
    public static function group(string $name, string $label, array $fields): array
    {
        return compact('name', 'label', 'fields') + ['type' => 'object', 'list' => false];
    }

    /**
     * A repeatable list of nested groups (cards).
     *
     * @param  array<int, array>  $fields
     */
    public static function list(string $name, string $label, array $fields, int $count = 0): array
    {
        return compact('name', 'label', 'fields') + ['type' => 'object', 'list' => true, 'defaultCount' => $count];
    }
}
