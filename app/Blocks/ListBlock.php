<?php

namespace App\Blocks;

use App\Blocks\Support\CardSlot;
use App\Models\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * ListBlock
 *
 * Abstract base class for all collection-powered list blocks (PackageList, BlogList, etc.).
 *
 * Subclasses only need to:
 *  1. Define $name, $label as usual.
 *  2. Override cardSchema() to declare which card slots they need.
 *  3. Override baseFields() to add their own block-level controls (taxonomy, per-page, etc.).
 *  4. Create a Blade view that uses the resolved $card object by slot key.
 *
 * ListBlock automatically:
 *  - Generates a "Select Collection" dropdown.
 *  - Generates one Field::select() per card slot, populated with the selected collection's fields.
 *  - Provides resolveCard($entry, $mappings) to resolve each slot value from entry data.
 *  - Provides collectionFieldOptions() for building clean, tagged option arrays.
 */
abstract class ListBlock extends Block
{
    /**
     * Define the card slots this list block needs to fill.
     * Each CardSlot has a key (used in templates), a label (shown in sidebar), and a type hint.
     *
     * Example:
     *   return [
     *       CardSlot::text('title',  'Card Title'),
     *       CardSlot::image('image', 'Card Thumbnail'),
     *       CardSlot::price('price', 'Current Price'),
     *   ];
     *
     * @return CardSlot[]
     */
    abstract protected function cardSchema(): array;

    /**
     * Block-level fields specific to the subclass (taxonomies, per-page, filters, etc.).
     * These are merged after the collection selector and card slot selectors.
     *
     * @return array<int, array>
     */
    protected function baseFields(): array
    {
        return [];
    }

    /**
     * Builds the full fields() array automatically:
     *   1. Select Collection dropdown.
     *   2. One Field::select() per card slot (mapped to the collection's inputs).
     *   3. Any extra fields from baseFields().
     *
     * @return array<int, array>
     */
    final public function fields(): array
    {
        $fields = [
            Field::select('listCollection', 'Select Collection', self::buildCollectionOptions(), default: ''),
        ];

        $fieldOptions = self::collectionFieldOptions();

        foreach ($this->cardSchema() as $slot) {
            $fields[] = Field::select(
                'map_'.$slot->key,
                $slot->label,
                $fieldOptions,
                default: ''
            );
        }

        foreach ($this->baseFields() as $field) {
            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Resolves a card object from a CollectionEntry using the admin-configured slot mappings.
     *
     * For each slot defined in cardSchema(), it looks up:
     *   1. The mapped collection input key from $mappings (e.g. $data['map_title'] = 'custom_name').
     *   2. Falls back to `$entry->title` for the first 'text' slot if value is empty.
     *
     * Returns a plain object with a property for each card slot key.
     *
     * @param  object  $entry  CollectionEntry model instance.
     * @param  array  $mappings  The block's saved $data array.
     */
    public function resolveCard(object $entry, array $mappings): object
    {
        $eData = $entry->data ?? [];
        $sections = $entry->sections ?? [];

        $card = ['_entry' => $entry];

        foreach ($this->cardSchema() as $slot) {
            $mappedKey = $mappings['map_'.$slot->key] ?? '';

            $value = null;

            if ($mappedKey !== '') {
                if ($mappedKey === 'created_at') {
                    $value = $entry->created_at ? (is_string($entry->created_at) ? $entry->created_at : $entry->created_at->format('M d, Y')) : null;
                } elseif ($mappedKey === 'updated_at') {
                    $value = $entry->updated_at ? (is_string($entry->updated_at) ? $entry->updated_at : $entry->updated_at->format('M d, Y')) : null;
                } elseif ($mappedKey === 'created_by') {
                    $value = $eData['created_by'] ?? $eData['author'] ?? ($entry->meta['author'] ?? ($entry->created_by ?? ($entry->author ?? (auth('admin')->user()->name ?? 'Admin'))));
                } elseif ($mappedKey === 'slug') {
                    $value = $entry->slug ?? null;
                } else {
                    // Look in entry data first
                    $value = $eData[$mappedKey] ?? ($entry->$mappedKey ?? null);

                    // Then search section data
                    if ($value === null || $value === '') {
                        foreach ($sections as $sec) {
                            $secData = $sec['data'] ?? [];
                            if (! empty($secData[$mappedKey])) {
                                $value = $secData[$mappedKey];
                                break;
                            }
                        }
                    }
                }
            }

            // For unmapped text slots, fall back to title if this is the first text slot
            if (($value === null || $value === '') && $slot->type === 'text' && $slot->key === 'title') {
                $value = $eData['title'] ?? $entry->title ?? null;
            }

            $card[$slot->key] = $value;
        }

        // Always attach entry metadata for Blade convenience
        $card['_link'] = $entry->route();
        $card['_slug'] = $entry->slug;

        return (object) $card;
    }

    /**
     * Builds the collection dropdown options (all non-pages collections).
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function buildCollectionOptions(): array
    {
        $options = [
            ['value' => '', 'label' => '-- Select Collection --'],
        ];

        try {
            if (Schema::hasTable('collections')) {
                $cols = Collection::select('slug', 'name')->get();
                foreach ($cols as $col) {
                    if ($col->slug !== 'pages') {
                        $options[] = [
                            'value' => $col->slug,
                            'label' => $col->name,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // DB not available yet (migrations running, etc.)
        }

        return $options;
    }

    /**
     * Builds tagged option arrays for card slot field selectors.
     *
     * Each option has:
     *   'value'           => collection field key (e.g. 'cover_photo')
     *   'label'           => human label (e.g. 'Cover Photo')
     *   'collection'      => collection slug (empty string = universal, shown for all collections)
     *   'collection_name' => collection name (e.g. 'Packages')
     *
     * The frontend (Alpine.js filteredSelectOptions) uses 'collection' to filter
     * options dynamically when the admin changes the "Select Collection" dropdown.
     *
     * @return array<int, array>
     */
    public static function collectionFieldOptions(): array
    {
        $options = [
            ['value' => '', 'label' => '-- Not Mapped --', 'collection' => ''],
            ['value' => 'title', 'label' => 'Title', 'collection' => ''],
            ['value' => 'slug', 'label' => 'Slug', 'collection' => ''],
            ['value' => 'created_at', 'label' => 'Created At', 'collection' => ''],
            ['value' => 'updated_at', 'label' => 'Updated At', 'collection' => ''],
            ['value' => 'created_by', 'label' => 'Created By', 'collection' => ''],
        ];

        try {
            if (Schema::hasTable('collections')) {
                $cols = Collection::select('id', 'name', 'slug', 'fields', 'enable_seo')->get();

                foreach ($cols as $col) {
                    // Skip pages collection and non-SEO collections (layouts, global sections, etc.)
                    if ($col->slug === 'pages' || $col->enable_seo === false) {
                        continue;
                    }

                    $fields = $col->fields ?? [];

                    if (! is_array($fields)) {
                        continue;
                    }

                    foreach ($fields as $f) {
                        $key = $f['template'] ?? $f['key'] ?? $f['name'] ?? $f['_key'] ?? null;
                        if (! $key) {
                            continue;
                        }

                        $label = $f['title'] ?? $f['label'] ?? Str::title(str_replace(['_', '-'], ' ', $key));

                        $options[] = [
                            'value' => $key,
                            'label' => $label,
                            'collection' => $col->slug,
                            'collection_name' => $col->name,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // DB not available yet
        }

        return $options;
    }
}
