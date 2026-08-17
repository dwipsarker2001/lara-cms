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
    public static function string(string $name, string $label, string $default = '', bool $multiline = false, string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'string', 'defaultValue' => $default, 'multiline' => $multiline];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function text(string $name, string $label, string $default = '', string $source = ''): array
    {
        return self::string($name, $label, $default, multiline: true, source: $source);
    }

    public static function number(string $name, string $label, int|float|string $default = '', string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'number', 'defaultValue' => (string) $default];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function boolean(string $name, string $label, bool $default = false, string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'boolean', 'defaultValue' => $default ? 'true' : 'false'];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function datetime(string $name, string $label, string $default = '', string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'datetime', 'defaultValue' => $default];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function date(string $name, string $label, string $default = '', string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'date', 'defaultValue' => $default];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function time(string $name, string $label, string $default = '', string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'time', 'defaultValue' => $default];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function image(string $name, string $label, string $default = '', string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'image', 'defaultValue' => $default];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function icon(string $name, string $label, string $default = '', string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'icon', 'defaultValue' => $default];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function map(string $name, string $label, string $default = '', string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'map', 'defaultValue' => $default];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function richText(string $name, string $label, string $default = '', string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'rich-text', 'defaultValue' => $default];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function link(string $name, string $label, string $default = '', string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'link', 'defaultValue' => $default];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function tags(string $name, string $label, string $source = ''): array
    {
        $field = compact('name', 'label') + ['type' => 'tags', 'defaultValue' => '[]'];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    public static function devices(string $name = 'devices', string $label = 'Screen Visibility'): array
    {
        return [
            'name' => $name,
            'label' => $label,
            'type' => 'devices',
            'defaultValue' => [
                'laptop' => true,
                'tablet' => true,
                'mobile' => true,
            ],
        ];
    }

    public static function configuration(): array
    {
        return [
            'name' => 'configuration',
            'label' => 'Configuration',
            'type' => 'object',
            'fields' => [
                self::devices('devices', 'Screen Visibility'),
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

    public static function background(): array
    {
        return self::configuration();
    }

    public static function select(string $name, string $label, array $options, string $default = '', string $source = ''): array
    {
        $field = compact('name', 'label', 'options') + ['type' => 'select', 'defaultValue' => $default];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    /**
     * Input field that lets the user select a field key from a selected Form (e.g. formId).
     */
    public static function form(string $name, string $label, string $formFieldKey = 'formId', string $default = '', string $source = ''): array
    {
        $field = compact('name', 'label', 'formFieldKey') + ['type' => 'form', 'defaultValue' => $default];
        if ($source !== '') {
            $field['source'] = $source;
        }

        return $field;
    }

    /**
     * Alias for Field::form()
     */
    public static function formField(string $name, string $label, string $formFieldKey = 'formId', string $default = '', string $source = ''): array
    {
        return self::form($name, $label, $formFieldKey, $default, $source);
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
