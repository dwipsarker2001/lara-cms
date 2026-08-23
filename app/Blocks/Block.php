<?php

namespace App\Blocks;

use App\Models\CollectionEntry;
use App\Models\Setting;
use App\Models\Term;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
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
     * Whether to auto-prepend a `configuration` group (containing Screen Visibility and/or Background settings).
     */
    public bool $configuration = true;

    /**
     * Whether to include background image/color/opacity inside the configuration group.
     */
    public bool $background = true;

    /**
     * Whether to include device visibility (laptop, tablet, mobile) inside the configuration group.
     */
    public bool $devices = true;

    /**
     * The block's editable fields.
     *
     * @return array<int, array>
     */
    abstract public function fields(): array;

    /** Custom blade view name if specified (e.g. "plugin-slug::blocks.my-block"). */
    public ?string $viewName = null;

    /** Blade view name. Defaults to custom viewName if set, otherwise checks co-located view.blade.php, blocks.custom.{kebab-name} or blocks.{kebab-name}. */
    public function view(): string
    {
        if (! empty($this->viewName)) {
            return $this->viewName;
        }

        // Check for co-located view in the same directory as the block class
        try {
            $ref = new \ReflectionClass($this);
            $dir = dirname($ref->getFileName() ?: '');
            if ($dir && is_dir($dir)) {
                $coLocatedView = $dir.DIRECTORY_SEPARATOR.'view.blade.php';
                $kebab = Str::kebab($this->name);
                $namedCoLocatedView = $dir.DIRECTORY_SEPARATOR.$kebab.'.blade.php';

                if (file_exists($coLocatedView)) {
                    $slug = 'block_'.md5($dir);
                    if (! view()->exists($slug.'::view')) {
                        app('view')->addNamespace($slug, $dir);
                    }

                    return $slug.'::view';
                }

                if (file_exists($namedCoLocatedView)) {
                    $slug = 'block_'.md5($dir);
                    if (! view()->exists($slug.'::'.$kebab)) {
                        app('view')->addNamespace($slug, $dir);
                    }

                    return $slug.'::'.$kebab;
                }
            }
        } catch (\Throwable $e) {
            // Fallback to standard view naming
        }

        $kebab = Str::kebab($this->name);

        if (view()->exists('blocks.custom.'.$kebab)) {
            return 'blocks.custom.'.$kebab;
        }

        return 'blocks.'.$kebab;
    }

    /**
     * Fields as the editor and renderer see them: `configuration` object group prepended
     * unless the block opts out or is global.
     *
     * @return array<int, array>
     */
    public function resolvedFields(): array
    {
        $fields = $this->fields();

        if ($this->configuration && ! $this->global) {
            $configFields = [];

            if ($this->devices) {
                $configFields[] = Field::devices('devices', 'Screen Visibility');
            }

            if ($this->background) {
                $configFields = array_merge($configFields, [
                    Field::image('image', 'Background Image'),
                    Field::select('color', 'Background Color', [
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
                    Field::number('opacity', 'Opacity', default: 100),
                ]);
            }

            if (! empty($configFields)) {
                $fields = [
                    Field::group('configuration', 'Configuration', $configFields),
                    ...$fields,
                ];
            }
        }

        return $fields;
    }

    public function render(array $data, string $_key = '', bool $preview = false, $page = null): string
    {
        if (! view()->exists($this->view())) {
            return '';
        }

        $resolvedFields = $this->resolvedFields();
        $sectionSources = is_array($data['_sources'] ?? null) ? $data['_sources'] : [];

        // Preload any collection entries and taxonomy terms referenced by sources
        $referencedEntryIds = [];
        $referencedTermIds = [];
        foreach ($sectionSources as $src) {
            if (is_string($src)) {
                if (str_starts_with($src, 'entry:')) {
                    $parts = explode(':', $src);
                    if (! empty($parts[1]) && is_numeric($parts[1])) {
                        $referencedEntryIds[] = (int) $parts[1];
                    }
                } elseif (str_starts_with($src, 'term:')) {
                    $parts = explode(':', $src);
                    if (! empty($parts[1]) && is_numeric($parts[1])) {
                        $referencedTermIds[] = (int) $parts[1];
                    }
                }
            }
        }

        $sourceEntries = empty($referencedEntryIds)
            ? collect()
            : CollectionEntry::with('collection')->whereIn('id', array_unique($referencedEntryIds))->get()->keyBy('id');

        $sourceTerms = empty($referencedTermIds)
            ? collect()
            : Term::with('taxonomy')->whereIn('id', array_unique($referencedTermIds))->get()->keyBy('id');

        $data = self::mergeSourceData($data, $resolvedFields, $page, $sectionSources, '', $sourceEntries, $sourceTerms);
        $data = self::hydrateCollectionReferences($data, $resolvedFields);
        $data = self::hydrateTaxonomyReferences($data, $resolvedFields);

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
    public static function mergeSourceData(array $data, array $fields, ?object $page = null, array $sectionSources = [], string $prefix = '', $sourceEntries = null, $sourceTerms = null): array
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
                                $item = self::mergeSourceData($item, $field['fields'] ?? [], $page, $sectionSources, $itemPath, $sourceEntries, $sourceTerms);
                            }
                        }
                    }
                } else {
                    if (isset($data[$name]) && is_array($data[$name])) {
                        $data[$name] = self::mergeSourceData($data[$name], $field['fields'] ?? [], $page, $sectionSources, $fullPath, $sourceEntries, $sourceTerms);
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
                    $entryValue = null;
                    if (str_starts_with($source, 'entry:')) {
                        $parts = explode(':', $source, 3);
                        $entryId = isset($parts[1]) ? (int) $parts[1] : null;
                        $key = $parts[2] ?? '';
                        $referencedEntry = $sourceEntries ? $sourceEntries->get($entryId) : ($entryId ? CollectionEntry::with('collection')->find($entryId) : null);
                        if ($referencedEntry) {
                            if ($key === 'title') {
                                $entryValue = $referencedEntry->title;
                            } elseif ($key === 'link' || $key === 'route' || $key === 'url') {
                                $entryValue = $referencedEntry->route();
                            } elseif ($key === 'slug') {
                                $entryValue = $referencedEntry->slug;
                            } else {
                                $rData = is_array($referencedEntry->data) ? $referencedEntry->data : [];
                                $entryValue = $rData[$key] ?? null;
                            }
                        }
                    } elseif (str_starts_with($source, 'term:')) {
                        $parts = explode(':', $source, 3);
                        $termId = isset($parts[1]) ? (int) $parts[1] : null;
                        $key = $parts[2] ?? '';
                        $referencedTerm = $sourceTerms ? $sourceTerms->get($termId) : ($termId ? Term::with('taxonomy')->find($termId) : null);
                        if ($referencedTerm) {
                            if ($key === 'title' || $key === 'name') {
                                $entryValue = $referencedTerm->title;
                            } elseif ($key === 'link' || $key === 'route' || $key === 'url') {
                                $entryValue = $referencedTerm->route();
                            } elseif ($key === 'slug') {
                                $entryValue = $referencedTerm->slug;
                            } elseif ($key === 'image') {
                                $tData = is_array($referencedTerm->data) ? $referencedTerm->data : [];
                                $entryValue = $tData['image'] ?? $tData['featured_image'] ?? $tData['cover_image'] ?? $tData['photo'] ?? null;
                            } else {
                                $tData = is_array($referencedTerm->data) ? $referencedTerm->data : [];
                                $entryValue = $tData[$key] ?? null;
                            }
                        }
                    } elseif ($page) {
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

    /**
     * Dynamically hydrate linked collection entries (e.g. package, destination, or collection entry pickers in cards/lists).
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, array>  $fields
     * @return array<string, mixed>
     */
    public static function hydrateCollectionReferences(array $data, array $fields): array
    {
        if (! Schema::hasTable('collection_entries')) {
            return $data;
        }

        // 1. Gather all collection entry IDs across the data hierarchy
        $entryIds = self::extractCollectionEntryIds($data, $fields);
        if (empty($entryIds)) {
            return $data;
        }

        // 2. Single batch load of all referenced entries
        $entries = CollectionEntry::with('collection')->whereIn('id', $entryIds)->get()->keyBy('id');
        if ($entries->isEmpty()) {
            return $data;
        }

        // 3. Hydrate data tree with live entry attributes
        return self::applyCollectionEntries($data, $fields, $entries);
    }

    /**
     * Recursively collect all referenced collection entry IDs.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, array>  $fields
     * @return array<int, int>
     */
    /**
     * Recursively collect all referenced collection entry IDs.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, array>  $fields
     * @return array<int, int>
     */
    protected static function extractCollectionEntryIds(array $data, array $fields): array
    {
        $ids = [];

        foreach ($fields as $field) {
            $name = $field['name'] ?? '';
            $type = $field['type'] ?? '';

            if ($type === 'object') {
                $subFields = $field['fields'] ?? [];
                if (! empty($field['list']) && isset($data[$name]) && is_array($data[$name])) {
                    foreach ($data[$name] as $item) {
                        if (is_array($item)) {
                            $ids = array_merge($ids, self::extractCollectionEntryIds($item, $subFields));
                            if (! empty($item['entry_id']) && is_numeric($item['entry_id'])) {
                                $ids[] = (int) $item['entry_id'];
                            }
                            if (! empty($item['package_id']) && is_numeric($item['package_id'])) {
                                $ids[] = (int) $item['package_id'];
                            }
                            if (! empty($item['deal_id']) && is_numeric($item['deal_id'])) {
                                $ids[] = (int) $item['deal_id'];
                            }
                            if (! empty($item['collection_entry_id']) && is_numeric($item['collection_entry_id'])) {
                                $ids[] = (int) $item['collection_entry_id'];
                            }
                        }
                    }
                } elseif (isset($data[$name]) && is_array($data[$name])) {
                    $ids = array_merge($ids, self::extractCollectionEntryIds($data[$name], $subFields));
                }
            } elseif ($type === 'collection' || $type === 'collectionEntry') {
                $val = $data[$name] ?? null;
                if (! empty($val)) {
                    if (is_numeric($val)) {
                        $ids[] = (int) $val;
                    } elseif (is_array($val) && ! empty($val['id']) && is_numeric($val['id'])) {
                        $ids[] = (int) $val['id'];
                    } elseif (is_array($val) && ! empty($val['entry_id']) && is_numeric($val['entry_id'])) {
                        $ids[] = (int) $val['entry_id'];
                    }
                }
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }

    /**
     * Recursively apply live collection entry attributes and attach _entry to items.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, array>  $fields
     * @param  Collection<int, CollectionEntry>  $entries
     * @return array<string, mixed>
     */
    protected static function applyCollectionEntries(array $data, array $fields, $entries): array
    {
        $collectionFieldNames = [];
        foreach ($fields as $f) {
            if (($f['type'] ?? '') === 'collection' || ($f['type'] ?? '') === 'collectionEntry') {
                $collectionFieldNames[] = $f['name'] ?? '';
            }
        }

        // Check if current data array itself references an entry
        $activeEntryId = null;
        foreach ($collectionFieldNames as $cfName) {
            if (! empty($data[$cfName])) {
                $rawVal = $data[$cfName];
                $activeEntryId = is_numeric($rawVal) ? (int) $rawVal : ($rawVal['id'] ?? ($rawVal['entry_id'] ?? null));
                if ($activeEntryId) {
                    break;
                }
            }
        }
        if (! $activeEntryId && ! empty($data['entry_id']) && is_numeric($data['entry_id'])) {
            $activeEntryId = (int) $data['entry_id'];
        }
        if (! $activeEntryId && ! empty($data['package_id']) && is_numeric($data['package_id'])) {
            $activeEntryId = (int) $data['package_id'];
        }
        if (! $activeEntryId && ! empty($data['deal_id']) && is_numeric($data['deal_id'])) {
            $activeEntryId = (int) $data['deal_id'];
        }

        if ($activeEntryId && $entries->has($activeEntryId)) {
            $entry = $entries->get($activeEntryId);
            $entryData = is_array($entry->data) ? $entry->data : [];

            $data['_entry'] = $entry;
            $data['_entry_data'] = $entryData;
            $data['_entry_title'] = $entry->title;
            $data['_entry_link'] = $entry->route();
            $data['_entry_slug'] = $entry->slug;
        }

        // Now process subfields / object / list structures
        foreach ($fields as $field) {
            $name = $field['name'] ?? '';
            $type = $field['type'] ?? '';

            if ($type === 'object') {
                $subFields = $field['fields'] ?? [];
                if (! empty($field['list']) && isset($data[$name]) && is_array($data[$name])) {
                    foreach ($data[$name] as $index => $item) {
                        if (is_array($item)) {
                            $data[$name][$index] = self::applyCollectionEntries($item, $subFields, $entries);
                        }
                    }
                } elseif (isset($data[$name]) && is_array($data[$name])) {
                    $data[$name] = self::applyCollectionEntries($data[$name], $subFields, $entries);
                }
            }
        }

        return $data;
    }

    /**
     * Batch hydrate all referenced Taxonomy Term models across the block data.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, array>  $fields
     * @return array<string, mixed>
     */
    public static function hydrateTaxonomyReferences(array $data, array $fields): array
    {
        if (! Schema::hasTable('terms')) {
            return $data;
        }

        $termIds = self::extractTaxonomyTermIds($data, $fields);
        if (empty($termIds)) {
            return $data;
        }

        $terms = Term::whereIn('id', $termIds)->get()->keyBy('id');
        if ($terms->isEmpty()) {
            return $data;
        }

        return self::applyTaxonomyTerms($data, $fields, $terms);
    }

    /**
     * Recursively collect all referenced taxonomy term IDs.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, array>  $fields
     * @return array<int, int>
     */
    protected static function extractTaxonomyTermIds(array $data, array $fields): array
    {
        $ids = [];

        foreach ($fields as $field) {
            $name = $field['name'] ?? '';
            $type = $field['type'] ?? '';

            if ($type === 'object') {
                $subFields = $field['fields'] ?? [];
                if (! empty($field['list']) && isset($data[$name]) && is_array($data[$name])) {
                    foreach ($data[$name] as $item) {
                        if (is_array($item)) {
                            $ids = array_merge($ids, self::extractTaxonomyTermIds($item, $subFields));
                            if (! empty($item['term_id']) && is_numeric($item['term_id'])) {
                                $ids[] = (int) $item['term_id'];
                            }
                            if (! empty($item['destination_id']) && is_numeric($item['destination_id'])) {
                                $ids[] = (int) $item['destination_id'];
                            }
                        }
                    }
                } elseif (isset($data[$name]) && is_array($data[$name])) {
                    $ids = array_merge($ids, self::extractTaxonomyTermIds($data[$name], $subFields));
                }
            } elseif ($type === 'taxonomies' || $type === 'taxonomy') {
                $val = $data[$name] ?? null;
                if (! empty($val)) {
                    if (is_numeric($val)) {
                        $ids[] = (int) $val;
                    } elseif (is_array($val)) {
                        foreach ($val as $subVal) {
                            if (is_numeric($subVal)) {
                                $ids[] = (int) $subVal;
                            } elseif (is_array($subVal) && ! empty($subVal['id']) && is_numeric($subVal['id'])) {
                                $ids[] = (int) $subVal['id'];
                            }
                        }
                    }
                }
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }

    /**
     * Recursively apply live taxonomy term attributes and attach _term to items.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, array>  $fields
     * @param  Collection<int, Term>  $terms
     * @return array<string, mixed>
     */
    protected static function applyTaxonomyTerms(array $data, array $fields, $terms): array
    {
        $taxFieldMap = [];
        foreach ($fields as $f) {
            if (($f['type'] ?? '') === 'taxonomies' || ($f['type'] ?? '') === 'taxonomy') {
                $fName = $f['name'] ?? '';
                if ($fName) {
                    $taxFieldMap[$fName] = $f;
                }
            }
        }

        $activeTermId = null;
        $activeFieldPattern = null;
        foreach ($taxFieldMap as $tfName => $fDef) {
            if (! empty($data[$tfName])) {
                $rawVal = $data[$tfName];
                $activeTermId = is_numeric($rawVal) ? (int) $rawVal : ($rawVal['id'] ?? null);
                if ($activeTermId) {
                    $activeFieldPattern = $fDef['route_pattern'] ?? null;
                    break;
                }
            }
        }
        if (! $activeTermId && ! empty($data['term_id']) && is_numeric($data['term_id'])) {
            $activeTermId = (int) $data['term_id'];
        }
        if (! $activeTermId && ! empty($data['destination_id']) && is_numeric($data['destination_id'])) {
            $activeTermId = (int) $data['destination_id'];
        }

        if ($activeTermId && $terms->has($activeTermId)) {
            $term = $terms->get($activeTermId);
            $termData = is_array($term->data) ? $term->data : [];
            $termLink = $term->route($activeFieldPattern);

            $data['_term'] = $term;
            $data['_term_data'] = $termData;
            $data['_term_title'] = $term->title;
            $data['_term_slug'] = $term->slug;
            $data['_term_link'] = $termLink;
        }

        // Process subfields / object / list structures
        foreach ($fields as $field) {
            $name = $field['name'] ?? '';
            $type = $field['type'] ?? '';

            if ($type === 'object') {
                $subFields = $field['fields'] ?? [];
                if (! empty($field['list']) && isset($data[$name]) && is_array($data[$name])) {
                    foreach ($data[$name] as $index => $item) {
                        if (is_array($item)) {
                            $data[$name][$index] = self::applyTaxonomyTerms($item, $subFields, $terms);
                        }
                    }
                } elseif (isset($data[$name]) && is_array($data[$name])) {
                    $data[$name] = self::applyTaxonomyTerms($data[$name], $subFields, $terms);
                }
            }
        }

        return $data;
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
