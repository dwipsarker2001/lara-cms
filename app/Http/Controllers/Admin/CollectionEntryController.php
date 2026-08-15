<?php

namespace App\Http\Controllers\Admin;

use App\Blocks\BlockRegistry;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Collection;
use App\Models\CollectionEntry;
use App\Models\Layout;
use App\Models\Setting;
use App\Support\Sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CollectionEntryController extends Controller
{
    public function index(Collection $collection)
    {
        $entries = $collection->entries()->orderBy('position')->get();

        return view('admin.collections.entries.index', compact('collection', 'entries'));
    }

    public function create(Collection $collection)
    {
        $admins = Admin::orderBy('name')->get();
        $layouts = Layout::where('collection', 'page')->orderBy('position')->orderBy('name')->get();

        return view('admin.collections.entries.create', compact('collection', 'admins', 'layouts'));
    }

    public function store(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'data' => 'required|array',
            'data.title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:collection_entries,slug',
            'meta' => 'nullable|array',
            'layout_id' => 'nullable|exists:layouts,id',
            'published' => 'boolean',
        ]);

        $entryMeta = array_merge($request->meta ?? [], [
            'metaTitleSource' => 'From Field',
            'metaDescriptionSource' => 'Inherit',
            'canonicalUrlSource' => 'Inherit',
            'schemaSource' => 'Inherit',
            'maxSnippetSource' => 'Inherit',
            'maxVideoPreviewSource' => 'Inherit',
            'socialImageSource' => 'Inherit',
            'xHandleSource' => 'Inherit',
            'xCardTitleSource' => 'Inherit',
            'xCardDescriptionSource' => 'Inherit',
        ]);

        // Copy sections from selected collection reference if exists
        $copiedSections = null;
        foreach ($collection->fields ?? [] as $field) {
            if (($field['type'] ?? '') === 'collection') {
                $key = $field['template'] ?? null;
                $val = $request->input("data.{$key}");
                if ($key && ! empty($val)) {
                    $selectedEntry = CollectionEntry::find($val);
                    if ($selectedEntry) {
                        $copiedSections = $selectedEntry->sections ?? [];
                        break;
                    }
                }
            }
        }

        $sections = $copiedSections ?? [];

        $entry = $collection->entries()->create([
            'data' => $request->input('data', []),
            'slug' => $data['slug'],
            'published' => $request->boolean('published', true),
            'meta' => $entryMeta,
            'sections' => $sections,
            'position' => $collection->entries()->max('position') + 1,
        ]);

        return redirect()->route('admin.collections.entries.editor', [$collection, $entry]);
    }

    public function edit(Collection $collection, CollectionEntry $entry)
    {
        abort_if((int) $entry->collection_id !== (int) $collection->id, 404);
        $admins = Admin::orderBy('name')->get();

        return view('admin.collections.entries.edit', compact('collection', 'entry', 'admins'));
    }

    public function update(Request $request, Collection $collection, CollectionEntry $entry)
    {
        abort_if((int) $entry->collection_id !== (int) $collection->id, 404);

        $data = $request->validate([
            'data' => 'nullable|array',
            'slug' => 'required|string|max:255|unique:collection_entries,slug,'.$entry->id,
            'meta' => 'nullable|array',
            'published' => 'boolean',
        ]);

        $entryData = [
            'data' => $data['data'] ?? $entry->data,
            'slug' => $data['slug'],
            'published' => $request->boolean('published', true),
        ];

        if ($request->has('meta')) {
            $entryData['meta'] = $request->meta;
        }

        $entry->update($entryData);

        return redirect()->route('admin.collections.entries.index', $collection)
            ->with('success', 'Entry updated successfully.');
    }

    public function editor(Collection $collection, CollectionEntry $entry)
    {
        abort_if((int) $entry->collection_id !== (int) $collection->id, 404);

        $registry = app(BlockRegistry::class);

        $blockList = collect($registry->pickerList())->map(function ($item) use ($registry) {
            $block = $registry->get($item['name']);
            $section = Sections::createDefaultSection($item['name']);
            $html = '';
            if ($block && $section) {
                $html = $block->render(
                    data: $section['data'],
                    _key: '',
                    preview: true,
                );
            }

            return [...$item, 'previewHtml' => $html];
        })->all();

        $homeGlobals = Sections::injectGlobals();

        $pages = collect();
        if (Schema::hasTable('collection_entries')) {
            $pages = CollectionEntry::whereHas('collection', fn ($q) => $q->where('slug', 'pages'))
                ->orderBy('position')
                ->get(['id', 'slug', 'data'])
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'route' => $p->route(),
                ]);
        }

        $entryCustomFields = collect([
            'title' => [
                'key' => 'title',
                'label' => 'Title',
            ],
            'slug' => [
                'key' => 'slug',
                'label' => 'Slug',
            ],
            'created_at' => [
                'key' => 'created_at',
                'label' => 'Created At',
            ],
            'updated_at' => [
                'key' => 'updated_at',
                'label' => 'Updated At',
            ],
            'created_by' => [
                'key' => 'created_by',
                'label' => 'Created By',
            ],
        ]);
        $excludedKeys = [];

        if (is_array($collection->fields)) {
            foreach ($collection->fields as $f) {
                $key = $f['template'] ?? $f['key'] ?? $f['name'] ?? '';
                if ($key !== '') {
                    if (($f['type'] ?? '') === 'collection') {
                        $excludedKeys[] = $key;

                        continue;
                    }
                    $entryCustomFields->put($key, [
                        'key' => $key,
                        'label' => $f['title'] ?? $f['label'] ?? Str::title(str_replace(['_', '-'], ' ', $key)),
                    ]);
                }
            }
        }

        if (is_array($entry->data)) {
            foreach (array_keys($entry->data) as $key) {
                if ($key !== '' && ! str_starts_with($key, '_') && ! $entryCustomFields->has($key) && ! in_array($key, $excludedKeys, true)) {
                    $entryCustomFields->put($key, [
                        'key' => $key,
                        'label' => Str::title(str_replace(['_', '-'], ' ', $key)),
                    ]);
                }
            }
        }

        $allGroupedFields = collect();

        if (! $collection->enable_seo) {
            // Layout Collection (Enable SEO is OFF): Include current collection fields + ALL other collections' fields
            if ($entryCustomFields->isNotEmpty()) {
                $allGroupedFields->push([
                    'collection_id' => $collection->id,
                    'name' => $collection->name.' (Current)',
                    'fields' => $entryCustomFields->values()->all(),
                ]);
            }

            $otherCollections = Collection::where('id', '!=', $collection->id)->orderBy('position')->orderBy('name')->get();
            foreach ($otherCollections as $otherCol) {
                $colFields = collect([
                    'title' => [
                        'key' => 'title',
                        'label' => 'Title',
                    ],
                    'slug' => [
                        'key' => 'slug',
                        'label' => 'Slug',
                    ],
                    'created_at' => [
                        'key' => 'created_at',
                        'label' => 'Created At',
                    ],
                    'updated_at' => [
                        'key' => 'updated_at',
                        'label' => 'Updated At',
                    ],
                    'created_by' => [
                        'key' => 'created_by',
                        'label' => 'Created By',
                    ],
                ]);
                if (is_array($otherCol->fields)) {
                    foreach ($otherCol->fields as $f) {
                        $key = $f['template'] ?? $f['key'] ?? $f['name'] ?? '';
                        if ($key !== '' && ($f['type'] ?? '') !== 'collection') {
                            $colFields->put($key, [
                                'key' => $key,
                                'label' => $f['title'] ?? $f['label'] ?? Str::title(str_replace(['_', '-'], ' ', $key)),
                            ]);
                        }
                    }
                }

                if ($colFields->isNotEmpty()) {
                    $allGroupedFields->push([
                        'collection_id' => $otherCol->id,
                        'name' => $otherCol->name,
                        'fields' => $colFields->values()->all(),
                    ]);
                }
            }
        } else {
            // Specific Content Collection (Enable SEO is ON): Include ONLY this specific collection's fields
            if ($entryCustomFields->isNotEmpty()) {
                $allGroupedFields->push([
                    'collection_id' => $collection->id,
                    'name' => $collection->name,
                    'fields' => $entryCustomFields->values()->all(),
                ]);
            }
        }

        // Add Site Settings inputs (custom_fields & core settings) to binding options
        $settings = Setting::first();
        $settingsCustomValues = [];
        $settingsFieldsList = collect();

        if ($settings) {
            $settingsCustomValues = is_array($settings->custom_values) ? $settings->custom_values : [];
            $settingsCustomValues['app_name'] = $settings->app_name ?? '';
            $settingsCustomValues['tagline'] = $settings->tagline ?? '';
            $settingsCustomValues['admin_email'] = $settings->admin_email ?? '';
            $settingsCustomValues['language'] = $settings->language ?? '';
            $settingsCustomValues['currency'] = $settings->currency ?? '';
            $settingsCustomValues['recaptcha_site_key'] = $settings->recaptcha_site_key ?? '';

            $settingsFieldsList->put('app_name', ['key' => 'app_name', 'label' => 'Site Name']);
            $settingsFieldsList->put('tagline', ['key' => 'tagline', 'label' => 'Site Tagline']);
            $settingsFieldsList->put('admin_email', ['key' => 'admin_email', 'label' => 'Admin Email']);
            $settingsFieldsList->put('language', ['key' => 'language', 'label' => 'Language']);
            $settingsFieldsList->put('currency', ['key' => 'currency', 'label' => 'Currency']);
            $settingsFieldsList->put('recaptcha_site_key', ['key' => 'recaptcha_site_key', 'label' => 'reCAPTCHA Site Key']);

            if (is_array($settings->custom_fields)) {
                foreach ($settings->custom_fields as $sf) {
                    $key = $sf['template'] ?? $sf['key'] ?? '';
                    if ($key !== '') {
                        $settingsFieldsList->put($key, [
                            'key' => $key,
                            'label' => $sf['title'] ?? Str::title(str_replace(['_', '-'], ' ', $key)),
                        ]);
                    }
                }
            }
        }

        if ($settingsFieldsList->isNotEmpty()) {
            $allGroupedFields->push([
                'collection_id' => 'site_settings',
                'name' => 'Site Settings',
                'fields' => $settingsFieldsList->values()->all(),
            ]);
        }

        $collectionFields = $entryCustomFields->values()->all();
        $groupedCollectionFields = $allGroupedFields->all();

        $entryData = array_merge([
            'title' => $entry->title,
            'slug' => $entry->slug ?? '',
            'created_at' => ! empty($entry->created_at) ? (is_string($entry->created_at) ? $entry->created_at : $entry->created_at->format('M d, Y')) : '',
            'updated_at' => ! empty($entry->updated_at) ? (is_string($entry->updated_at) ? $entry->updated_at : $entry->updated_at->format('M d, Y')) : '',
            'created_by' => $entry->data['created_by'] ?? $entry->data['author'] ?? ($entry->meta['author'] ?? (auth('admin')->check() ? auth('admin')->user()->name : 'Admin')),
        ], is_array($entry->data) ? $entry->data : []);

        return view('admin.collections.entries.editor', [
            'collection' => $collection,
            'entry' => $entry,
            'entryData' => $entryData,
            'blockSchemas' => $registry->schemas(),
            'homeGlobals' => $homeGlobals,
            'blockList' => $blockList,
            'pages' => $pages,
            'collectionFields' => $collectionFields,
            'groupedCollectionFields' => $groupedCollectionFields,
            'settingsCustomValues' => $settingsCustomValues,
        ]);
    }

    public function updateSections(Request $request, Collection $collection, CollectionEntry $entry)
    {
        abort_if((int) $entry->collection_id !== (int) $collection->id, 404);

        $request->validate([
            'sections' => 'present|array',
            'sections.*._key' => 'required|string',
            'sections.*.name' => 'required|string',
            'sections.*.data' => 'required',
        ]);

        $entry->update(['sections' => $request->sections]);

        $registry = app(BlockRegistry::class);
        $globalNames = $registry->globals()->pluck('name')->toArray();

        $propagated = Sections::sectionsToPropagate($request->sections, $globalNames);

        if (! empty($propagated)) {
            $home = CollectionEntry::where('slug', 'home')->first();

            if ($home) {
                $homeSections = collect($home->sections)->map(function ($s) use ($propagated) {
                    $match = collect($propagated)->firstWhere('name', $s['name']);

                    return $match ? [...$s, 'data' => $match['data']] : $s;
                })->all();

                $home->update(['sections' => $homeSections]);
            }
        }

        return response()->json(['message' => 'Sections saved.']);
    }

    public function destroy(Collection $collection, CollectionEntry $entry)
    {
        abort_if((int) $entry->collection_id !== (int) $collection->id, 404);

        $entry->delete();

        return redirect()->route('admin.collections.entries.index', $collection)
            ->with('success', 'Entry deleted successfully.');
    }

    public function reorder(Request $request, Collection $collection)
    {
        foreach ($request->entry_ids ?? [] as $i => $id) {
            CollectionEntry::where('collection_id', $collection->id)
                ->where('id', $id)
                ->update(['position' => $i]);
        }

        return response()->noContent();
    }
}
