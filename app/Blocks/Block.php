<?php

namespace App\Blocks;

use App\Models\Setting;
use Illuminate\Support\Str;

/**
 * Block
 * Base class for every content block. To create a new section a developer only:
 *
 *   1. Extends this class in app/Blocks (any subfolder), sets $name + $label,
 *      and returns the editable fields from fields().
 *   2. Creates the matching Blade view resources/views/blocks/{kebab-name}.blade.php
 *      which receives $data and renders the HTML.
 *
 * Everything else — registration, the editor form, default content, public
 * rendering — is automatic.
 */
abstract class Block
{
    /** Machine name, e.g. "heroBanner". Referenced by sections and the view. */
    public string $name;

    /** Human label shown in the block picker. */
    public string $label;

    /** Global blocks (navbar/footer) are shared site-wide, edited on the home page. */
    public bool $global = false;

    /**
     * Whether to auto-prepend a `background` field (image/color/opacity wrapper).
     * Full-bleed blocks with their own background (hero, navbar) set this false.
     */
    public bool $background = true;

    /**
     * Whether to auto-prepend a `devices` field (device visibility toggle: laptop, tablet, mobile).
     */
    public bool $devices = true;

    /**
     * The block's editable fields.
     *
     * @return array<int, array>
     */
    abstract public function fields(): array;

    /** Blade view name. Defaults to blocks.custom.{kebab-name} if it exists, otherwise blocks.{kebab-name}. */
    public function view(): string
    {
        $kebab = Str::kebab($this->name);

        if (view()->exists('blocks.custom.'.$kebab)) {
            return 'blocks.custom.'.$kebab;
        }

        return 'blocks.'.$kebab;
    }

    /**
     * Fields as the editor and renderer see them: `background` and `devices` fields prepended
     * unless the block opts out or is global.
     *
     * @return array<int, array>
     */
    public function resolvedFields(): array
    {
        $fields = $this->fields();

        if ($this->devices && ! $this->global) {
            $fields = [Field::devices(), ...$fields];
        }

        if ($this->background && ! $this->global) {
            $fields = [Field::background(), ...$fields];
        }

        return $fields;
    }

    public function render(array $data, string $_key = '', bool $preview = false, $page = null): string
    {
        if (! view()->exists($this->view())) {
            return '';
        }

        if ($page) {
            $sectionSources = is_array($data['_sources'] ?? null) ? $data['_sources'] : [];
            $data = self::mergeSourceData($data, $this->resolvedFields(), $page, $sectionSources);
        }

        return view($this->view(), compact('data', '_key', 'preview', 'page'))->render();
    }

    /**
     * Overlay entry custom-field values onto block data for fields that declare a source or visual binding.
     * Source values take priority if non-empty; block data is the fallback.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, array>  $fields
     * @param  array<string, string>  $sectionSources
     * @return array<string, mixed>
     */
    public static function mergeSourceData(array $data, array $fields, object $page, array $sectionSources = [], string $prefix = ''): array
    {
        $entryData = is_array($page->data ?? null) ? $page->data : [];

        foreach ($fields as $field) {
            $name = $field['name'] ?? '';
            if ($name === '') {
                continue;
            }

            $fullPath = $prefix !== '' ? ($prefix.'.'.$name) : $name;

            if (($field['type'] ?? '') === 'object') {
                if (! empty($field['list'])) {
                    if (isset($data[$name]) && is_array($data[$name])) {
                        foreach ($data[$name] as $index => &$item) {
                            if (is_array($item)) {
                                $itemPath = $fullPath.'.'.$index;
                                $item = self::mergeSourceData($item, $field['fields'] ?? [], $page, $sectionSources, $itemPath);
                            }
                        }
                    }
                } else {
                    if (isset($data[$name]) && is_array($data[$name])) {
                        $data[$name] = self::mergeSourceData($data[$name], $field['fields'] ?? [], $page, $sectionSources, $fullPath);
                    }
                }
            } else {
                $genericPath = preg_replace('/\.\d+\./', '.', $fullPath);

                if (array_key_exists($fullPath, $sectionSources)) {
                    $source = $sectionSources[$fullPath];
                } elseif (array_key_exists($genericPath, $sectionSources)) {
                    $source = $sectionSources[$genericPath];
                } elseif (array_key_exists($name, $sectionSources)) {
                    $source = $sectionSources[$name];
                } else {
                    $source = $field['source'] ?? '';
                }

                if ($source === '__none__') {
                    continue;
                }

                if ($source !== '') {
                    $entryValue = $entryData[$source] ?? ($page->$source ?? null);
                    if ($entryValue === null || $entryValue === '') {
                        if ($source === 'created_by') {
                            $eData = is_array($entryData) ? $entryData : [];
                            $entryValue = $eData['created_by'] ?? $eData['author'] ?? (is_object($page) ? ($page->data['created_by'] ?? $page->data['author'] ?? ($page->meta['author'] ?? ($page->created_by ?? ($page->author ?? null)))) : null);
                            if (empty($entryValue)) {
                                $entryValue = auth('admin')->user()->name ?? 'Admin';
                            }
                        } else {
                            $settings = Setting::first();
                            if ($settings) {
                                $customValues = is_array($settings->custom_values) ? $settings->custom_values : [];
                                if (array_key_exists($source, $customValues)) {
                                    $entryValue = $customValues[$source];
                                } elseif (isset($settings->$source)) {
                                    $entryValue = $settings->$source;
                                }
                            }
                        }
                    }
                    if ($entryValue instanceof \DateTimeInterface) {
                        $entryValue = $entryValue->format('M d, Y');
                    }
                    if ($entryValue !== null && $entryValue !== '') {
                        if (is_array($entryValue) && ($field['type'] ?? '') !== 'object' && ($field['type'] ?? '') !== 'location') {
                            if (! empty($entryValue['formatted']) && is_string($entryValue['formatted'])) {
                                $data[$name] = $entryValue['formatted'];
                            } else {
                                $parts = array_filter([$entryValue['city'] ?? null, $entryValue['state'] ?? null, $entryValue['country'] ?? null]);
                                if (! empty($parts)) {
                                    $data[$name] = implode(', ', $parts);
                                } elseif (! empty($entryValue['name']) && is_string($entryValue['name'])) {
                                    $data[$name] = $entryValue['name'];
                                } elseif (! empty($entryValue['title']) && is_string($entryValue['title'])) {
                                    $data[$name] = $entryValue['title'];
                                } elseif (! empty($entryValue['url']) && is_string($entryValue['url'])) {
                                    $data[$name] = $entryValue['url'];
                                } else {
                                    $data[$name] = '';
                                }
                            }
                        } else {
                            $data[$name] = $entryValue;
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Parse any map input (Google Maps share link, iframe tag HTML snippet, direct embed URL, or image URL)
     * into a normalized rendering array.
     *
     * @return array{type: 'iframe'|'image'|'empty', url: string}
     */
    public static function parseMapValue(?string $value): array
    {
        $value = trim($value ?? '');
        if ($value === '') {
            return ['type' => 'empty', 'url' => ''];
        }

        // 1. Raw <iframe> tag pasted by user
        if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $value, $matches)) {
            $src = html_entity_decode($matches[1]);

            return ['type' => 'iframe', 'url' => $src];
        }

        // 2. Google Maps share links (maps.app.goo.gl, goo.gl/maps, google.com/maps/place, etc.)
        if (preg_match('/(maps\.app\.goo\.gl|goo\.gl\/maps|google\.com\/maps\/place)/i', $value)) {
            if (! str_contains($value, 'output=embed') && ! str_contains($value, '/embed')) {
                return ['type' => 'iframe', 'url' => 'https://maps.google.com/maps?q='.urlencode($value).'&output=embed'];
            }
        }

        // 3. Direct embed URLs (google.com/maps, openstreetmap.org, output=embed)
        if (str_contains($value, 'google.com/maps') || str_contains($value, 'openstreetmap.org') || str_contains($value, 'output=embed')) {
            return ['type' => 'iframe', 'url' => $value];
        }

        // 4. Fallback to image URL or asset
        return ['type' => 'image', 'url' => $value];
    }

    /** Serializable shape handed to the admin editor (JSON). */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'global' => $this->global,
            'fields' => $this->resolvedFields(),
        ];
    }
}
