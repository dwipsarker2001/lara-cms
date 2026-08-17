@extends('admin.layout')

@section('title', $form->title . ' — Entries')
@section('breadcrumb', 'Form Entries')

@section('content')
@php
    $formFieldNames = collect($form->fields ?? [])
        ->filter(fn ($field) => is_array($field) && filled($field['name'] ?? null))
        ->pluck('name')
        ->toArray();

    $entryDataKeys = $entries->flatMap(fn ($entry) => is_array($entry->data) ? array_keys($entry->data) : [])
        ->unique()
        ->values()
        ->toArray();

    $allKeys = array_unique(array_merge($formFieldNames, $entryDataKeys));

    $fields = collect($allKeys)->map(function ($key) use ($form) {
        $schemaField = collect($form->fields ?? [])->firstWhere('name', $key);
        return [
            'name' => $key,
            'label' => !empty($schemaField['column_name']) 
                ? $schemaField['column_name'] 
                : (!empty($schemaField['label']) ? $schemaField['label'] : str($key)->replace('_', ' ')->title()->toString()),
        ];
    });

    $headers = [];
    foreach ($fields as $field) {
        $headers[] = ['key' => $field['name'], 'label' => $field['label']];
    }
    $headers[] = ['key' => 'created', 'label' => 'Submitted'];
    $headers[] = ['key' => 'actions', 'label' => 'Actions'];

    $isColumnVisible = function (string $columnKey) use ($savedColumns): bool {
        if (! is_array($savedColumns)) {
            return true;
        }
        return ($savedColumns[$columnKey] ?? true) !== false;
    };
@endphp

<div class="max-w-5xl mx-auto px-2 sm:px-0" x-data="formEntriesPage()">
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            <span class="flex size-6 shrink-0 items-center justify-center text-text-muted">
                @if($form->icon)
                    <i class="{{ $form->icon }} text-lg"></i>
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <line x1="3" y1="9" x2="21" y2="9" />
                        <line x1="9" y1="21" x2="9" y2="9" />
                    </svg>
                @endif
            </span>
            {{ $form->title }}
        </h1>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.forms.edit', $form) }}"
                class="size-10 flex items-center justify-center rounded-lg border border-content-border bg-white text-text-primary hover:bg-gray-50 transition-colors shadow-sm"
                title="Form Settings"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
                    <circle cx="12" cy="12" r="3" />
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
            </a>
            <a href="{{ route('admin.forms.export', $form) }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-primary border border-content-border shadow-sm"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path d="M10 1a1 1 0 011 1v9.586l2.293-2.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 11.586V2a1 1 0 011-1z" />
                    <path d="M2 16a1 1 0 011 1v1h12v-1a1 1 0 112 0v2a1 1 0 01-1 1H2a1 1 0 01-1-1v-2a1 1 0 011-1z" />
                </svg>
                <span>Export</span>
            </a>
            <a href="{{ route('admin.forms.entries.create', $form) }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                <span>Add Entry</span>
            </a>
        </div>
    </header>

    <div class="bg-panel-bg rounded-2xl p-[7px] mb-8">
        <div class="flex flex-wrap items-center justify-between gap-3 px-2 pb-2.5 pt-1">
            <span class="flex items-center gap-2 text-[14px] font-medium text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <line x1="3" y1="9" x2="21" y2="9" />
                    <line x1="9" y1="21" x2="9" y2="9" />
                </svg>
                All Submissions
            </span>
            @if(!$entries->isEmpty())
                <div class="flex items-center gap-2 flex-nowrap shrink-0">
                    {{-- Search Input --}}
                    <div class="relative shrink-0">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-text-muted">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Search submissions..."
                            aria-label="Search submissions"
                            class="h-8 w-44 sm:w-56 rounded-lg border border-content-border bg-content-bg pl-8 pr-3 text-[12px] text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/10 shadow-sm"
                        >
                    </div>

                    {{-- Filter Dropdown --}}
                    <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button"
                            @click="open = !open"
                            class="flex h-8 items-center gap-1.5 whitespace-nowrap rounded-lg border border-content-border bg-white px-3 text-[12px] font-medium text-text-heading hover:bg-body-bg shadow-sm transition-colors cursor-pointer">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-text-muted shrink-0">
                                <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                            </svg>
                            <span x-text="filterColumnLabel" class="whitespace-nowrap">Filter: All</span>
                            <svg class="size-3 text-text-muted shrink-0 transition-transform ml-0.5" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                            class="absolute right-0 top-full mt-2 min-w-[14rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 space-y-0.5 z-[100]">
                            <button type="button" @click="filterColumn = 'all'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === 'all' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>All Columns</span>
                                <span x-show="filterColumn === 'all'" class="font-bold">✓</span>
                            </button>
                            @foreach ($fields as $field)
                                <button type="button" @click="filterColumn = '{{ $field['name'] }}'; open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="filterColumn === '{{ $field['name'] }}' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                    <span>{{ $field['label'] }}</span>
                                    <span x-show="filterColumn === '{{ $field['name'] }}'" class="font-bold">✓</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Sort Dropdown --}}
                    <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button"
                            @click="open = !open"
                            title="Sort Table"
                            class="flex size-8 items-center justify-center rounded-lg border border-content-border bg-white text-text-muted hover:text-text-heading hover:bg-body-bg shadow-sm transition-colors cursor-pointer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0">
                                <path d="m3 16 4 4 4-4" />
                                <path d="M7 20V4" />
                                <path d="m21 8-4-4-4 4" />
                                <path d="M17 4v16" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                            class="absolute right-0 top-full mt-2 min-w-[14rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 space-y-0.5 z-[100]">
                            <button type="button" @click="sortColumn = 'created'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortColumn === 'created' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Submitted Date</span>
                                <span x-show="sortColumn === 'created'" class="font-bold">✓</span>
                            </button>
                            @foreach ($fields as $field)
                                <button type="button" @click="sortColumn = '{{ $field['name'] }}'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortColumn === '{{ $field['name'] }}' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                    <span>{{ $field['label'] }}</span>
                                    <span x-show="sortColumn === '{{ $field['name'] }}'" class="font-bold">✓</span>
                                </button>
                            @endforeach

                            <div class="my-1 border-t border-content-border"></div>

                            <button type="button" @click="sortDirection = 'asc'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortDirection === 'asc' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Ascending (A-Z / Oldest)</span>
                                <span x-show="sortDirection === 'asc'" class="font-bold">✓</span>
                            </button>
                            <button type="button" @click="sortDirection = 'desc'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortDirection === 'desc' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>Descending (Z-A / Newest)</span>
                                <span x-show="sortDirection === 'desc'" class="font-bold">✓</span>
                            </button>
                        </div>
                    </div>

                    {{-- Column Settings Dropdown --}}
                    <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button"
                            @click="open = !open"
                            title="Column Settings"
                            class="flex size-8 items-center justify-center rounded-lg border border-content-border bg-white text-text-muted hover:text-text-heading hover:bg-body-bg shadow-sm transition-colors cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-settings">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.72l-.15.1a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.51a2 2 0 0 1 1-1.72l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                            class="absolute right-0 top-full mt-2 min-w-[15rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-2 space-y-1 z-[100]">
                            <div class="px-2 py-1 text-[11px] font-semibold text-text-muted uppercase tracking-wider">
                                Display Columns
                            </div>
                            <div class="my-1 border-t border-content-border"></div>
                            @foreach ($fields as $field)
                                <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                    <input type="checkbox" x-model="visibleColumns['{{ $field['name'] }}']" @change="saveColumnPreferences()" class="rounded border-content-border text-primary focus:ring-primary/20">
                                    <span>{{ $field['label'] }}</span>
                                </label>
                            @endforeach
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['created']" @change="saveColumnPreferences()" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span>Submitted Date</span>
                            </label>
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['actions']" @change="saveColumnPreferences()" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span>Actions</span>
                            </label>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="px-1.5 pb-2">
            @if($entries->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center px-6">
                    <img src="/empty-collection.svg" alt="No items" class="size-28 mb-3 opacity-60">
                    <p class="text-sm font-medium text-text-heading">No entries yet.</p>
                    <p class="text-xs text-text-muted mt-1">Submissions for “{{ $form->title }}” will appear here.</p>
                </div>
            @else
                <div class="rounded-xl ring-1 ring-content-border bg-content-bg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto table-scrollbar">
                        <table class="w-full min-w-full border-separate border-spacing-y-0 text-left text-[13px]">
                            <thead>
                                <tr class="bg-[#f9fafb]">
                                    <th class="w-10 px-4 py-3 border-b border-content-border rounded-tl-xl text-center">
                                        <input type="checkbox"
                                            @change="toggleSelectAll($event)"
                                            :checked="allSelected"
                                            class="size-4 rounded border-content-border text-primary focus:ring-primary/20 cursor-pointer"
                                        >
                                    </th>
                                    @foreach ($fields as $field)
                                        <th x-show="visibleColumns['{{ $field['name'] }}'] !== false" @if(!$isColumnVisible($field['name'])) style="display: none;" @endif class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                            <button @click="sortColumn = '{{ $field['name'] }}'; sortRows()" class="cursor-pointer hover:text-text-heading">{{ $field['label'] }}</button>
                                        </th>
                                    @endforeach
                                    <th x-show="visibleColumns['created'] !== false" @if(!$isColumnVisible('created')) style="display: none;" @endif class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                        <button @click="sortColumn = 'created'; sortRows()" class="cursor-pointer hover:text-text-heading">Submitted</button>
                                    </th>
                                    <th x-show="visibleColumns['actions'] !== false" @if(!$isColumnVisible('actions')) style="display: none;" @endif class="whitespace-nowrap px-3 py-3 font-medium text-text-muted text-[12px] border-b border-content-border sticky right-0 bg-[#f9fafb] z-20 text-right rounded-tr-xl w-14">Actions</th>
                                </tr>
                            </thead>
                            <tbody x-ref="tbody">
                                @foreach($entries as $entry)
                                    <tr data-sortable
                                        data-id="{{ $entry->id }}"
                                        data-created="{{ $entry->created_at->timestamp }}"
                                        @foreach($fields as $f)
                                            data-field-{{ $f['name'] }}="{{ strtolower(is_array($entry->data[$f['name']] ?? null) ? implode(' ', $entry->data[$f['name']]) : (string)($entry->data[$f['name']] ?? '')) }}"
                                        @endforeach
                                        x-show="matchesSearch({{ json_encode($entry->data) }}, {{ $entry->id }}, {{ json_encode($entry->created_at->format('M j, Y g:i A')) }})"
                                        class="group hover:bg-[#f9fafb] transition-colors"
                                    >
                                        <td class="w-10 px-4 py-3 border-b border-content-border group-last:border-b-0 group-last:rounded-bl-xl text-center">
                                            <input type="checkbox"
                                                value="{{ $entry->id }}"
                                                x-model="selectedEntries"
                                                class="size-4 rounded border-content-border text-primary focus:ring-primary/20 cursor-pointer"
                                            >
                                        </td>

                                        @foreach ($fields as $field)
                                            @php
                                                $value = $entry->data[$field['name']] ?? null;
                                                if (is_array($value)) {
                                                    $value = implode(', ', $value);
                                                } elseif (is_bool($value)) {
                                                    $value = $value ? 'Yes' : 'No';
                                                }
                                                if (is_string($value)) {
                                                    $value = str_replace(["\r\n", "\r", "\n"], ' ', $value);
                                                }
                                            @endphp
                                            <td x-show="visibleColumns['{{ $field['name'] }}'] !== false" @if(!$isColumnVisible($field['name'])) style="display: none;" @endif class="px-4 py-3 text-text-primary max-w-[200px] whitespace-nowrap overflow-hidden border-b border-content-border group-last:border-b-0">
                                                <span class="block max-w-[200px] truncate" title="{{ is_scalar($value) ? $value : '' }}">
                                                    {{ filled($value) || $value === 0 || $value === '0' ? $value : '—' }}
                                                </span>
                                            </td>
                                        @endforeach

                                        <td x-show="visibleColumns['created'] !== false" @if(!$isColumnVisible('created')) style="display: none;" @endif class="px-4 py-3 text-text-primary whitespace-nowrap min-w-[160px] border-b border-content-border group-last:border-b-0">
                                            <span>{{ $entry->created_at->format('M j, Y g:i A') }}</span>
                                        </td>
                                        <td x-show="visibleColumns['actions'] !== false"
                                            @if(!$isColumnVisible('actions')) style="display: none;" @endif
                                            x-data="{ open: false }"
                                            @click.outside="open = false"
                                            @keydown.escape.window="open = false"
                                            :class="open ? 'z-50' : 'z-10'"
                                            class="sticky right-0 bg-white group-hover:bg-[#f9fafb] group-last:rounded-br-xl px-3 py-2 text-right whitespace-nowrap transition-colors border-b border-content-border group-last:border-b-0 w-14">
                                            <div class="relative inline-block text-left">
                                                <button
                                                    type="button"
                                                    @click="open = !open"
                                                    class="size-8 inline-flex items-center justify-center rounded-lg border border-content-border bg-white text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors shadow-2xs cursor-pointer"
                                                    title="More Actions"
                                                    aria-label="More Actions"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-more-horizontal">
                                                        <circle cx="12" cy="12" r="1"/>
                                                        <circle cx="19" cy="12" r="1"/>
                                                        <circle cx="5" cy="12" r="1"/>
                                                    </svg>
                                                </button>
                                                <div
                                                    x-show="open"
                                                    x-cloak
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    role="menu"
                                                    style="position: fixed; z-index: 99999;"
                                                    :style="open ? { top: ($el.parentElement.getBoundingClientRect().bottom + 4) + 'px', left: ($el.parentElement.getBoundingClientRect().right - 144) + 'px' } : {}"
                                                    class="w-36 rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 text-left"
                                                >
                                                    <button type="button" role="menuitem"
                                                        @click="$dispatch('open-entry-detail', { id: {{ $entry->id }} }); open = false"
                                                        class="flex w-full items-center justify-start gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye shrink-0 text-text-muted">
                                                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                                            <circle cx="12" cy="12" r="3"/>
                                                        </svg>
                                                        <span>View</span>
                                                    </button>
                                                    <button type="button" role="menuitem"
                                                        @click="$dispatch('open-entry-edit', { id: {{ $entry->id }} }); open = false"
                                                        class="flex w-full items-center justify-start gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil shrink-0 text-text-muted">
                                                            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                                                            <path d="m15 5 4 4"/>
                                                        </svg>
                                                        <span>Edit</span>
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.forms.entries.duplicate', [$form, $entry]) }}" class="w-full mb-0">
                                                        @csrf
                                                        <button type="submit" role="menuitem"
                                                            class="flex w-full items-center justify-start gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors text-text-primary hover:bg-body-bg cursor-pointer"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-copy shrink-0 text-text-muted">
                                                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                                                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                                            </svg>
                                                            <span>Duplicate</span>
                                                        </button>
                                                    </form>
                                                    <hr class="my-1 border-content-border">
                                                    <button type="button" role="menuitem"
                                                        @click="deletingEntry = {{ $entry->id }}; open = false"
                                                        class="flex w-full items-center justify-start gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2 shrink-0 text-red-500">
                                                            <path d="M3 6h18"/>
                                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                                        </svg>
                                                        <span>Delete</span>
                                                    </button>
                                                    <form id="delete-entry-form-{{ $entry->id }}" method="POST" action="{{ route('admin.forms.entries.destroy', [$form, $entry]) }}" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($entries->hasPages())
                    <footer class="flex justify-between flex-wrap items-center antialiased pt-3">
                        <div class="text-sm text-text-muted">
                            Showing {{ $entries->firstItem() }}–{{ $entries->lastItem() }} of {{ $entries->total() }}
                        </div>
                        <div class="flex items-center gap-1">
                            {{-- Previous Page Link --}}
                            @if($entries->onFirstPage())
                                <button disabled class="inline-flex items-center justify-center size-8 rounded-full text-text-heading opacity-40 cursor-not-allowed">
                                    <svg viewBox="0 0 15 15" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M8.842 3.135a.5.5 0 01.023.707L5.435 7.5l3.43 3.658a.5.5 0 01-.73.684l-3.75-4a.5.5 0 010-.684l3.75-4a.5.5 0 01.707-.023" clip-rule="evenodd" /></svg>
                                </button>
                            @else
                                <a href="{{ $entries->previousPageUrl() }}" class="inline-flex items-center justify-center size-8 rounded-full hover:bg-gray-400/10 text-text-heading transition-colors">
                                    <svg viewBox="0 0 15 15" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M8.842 3.135a.5.5 0 01.023.707L5.435 7.5l3.43 3.658a.5.5 0 01-.73.684l-3.75-4a.5.5 0 010-.684l3.75-4a.5.5 0 01.707-.023" clip-rule="evenodd" /></svg>
                                </a>
                            @endif

                            {{-- Circular Page Buttons --}}
                            @php
                                $current = $entries->currentPage();
                                $last = $entries->lastPage();
                                $start = max(1, $current - 1);
                                $end = min($last, $current + 1);
                            @endphp

                            @if($start > 1)
                                <a href="{{ $entries->url(1) }}" class="inline-flex items-center justify-center size-8 rounded-full text-xs font-semibold text-gray-500 hover:bg-gray-200/60 hover:text-gray-900 transition-colors">1</a>
                                @if($start > 2)
                                    <span class="px-1 text-xs text-gray-400 select-none">...</span>
                                @endif
                            @endif

                            @for($page = $start; $page <= $end; $page++)
                                @if($page === $current)
                                    <span class="inline-flex items-center justify-center size-8 rounded-full bg-gray-300 text-gray-900 text-xs font-bold shadow-xs">{{ $page }}</span>
                                @else
                                    <a href="{{ $entries->url($page) }}" class="inline-flex items-center justify-center size-8 rounded-full text-xs font-semibold text-gray-500 hover:bg-gray-200/60 hover:text-gray-900 transition-colors">{{ $page }}</a>
                                @endif
                            @endfor

                            @if($end < $last)
                                @if($end < $last - 1)
                                    <span class="px-1 text-xs text-gray-400 select-none">...</span>
                                @endif
                                <a href="{{ $entries->url($last) }}" class="inline-flex items-center justify-center size-8 rounded-full text-xs font-semibold text-gray-500 hover:bg-gray-200/60 hover:text-gray-900 transition-colors">{{ $last }}</a>
                            @endif

                            {{-- Next Page Link --}}
                            @if($entries->hasMorePages())
                                <a href="{{ $entries->nextPageUrl() }}" class="inline-flex items-center justify-center size-8 rounded-full hover:bg-gray-400/10 text-text-heading transition-colors">
                                    <svg viewBox="0 0 15 15" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M6.158 3.135a.5.5 0 01-.023.707L9.565 7.5l-3.43 3.658a.5.5 0 00.73.684l3.75-4a.5.5 0 000-.684l-3.75-4a.5.5 0 00-.707-.023" clip-rule="evenodd" /></svg>
                                </a>
                            @else
                                <button disabled class="inline-flex items-center justify-center size-8 rounded-full text-text-heading opacity-40 cursor-not-allowed">
                                    <svg viewBox="0 0 15 15" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M6.158 3.135a.5.5 0 01-.023.707L9.565 7.5l-3.43 3.658a.5.5 0 00.73.684l3.75-4a.5.5 0 000-.684l-3.75-4a.5.5 0 00-.707-.023" clip-rule="evenodd" /></svg>
                                </button>
                            @endif
                        </div>
                        {{-- Interactive Per Page Dropdown --}}
                        <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                            <div class="flex items-center gap-2 text-xs text-text-muted font-medium">
                                <span>Per Page</span>
                                <button type="button" @click="open = !open"
                                    class="inline-flex items-center justify-between gap-1.5 bg-white border border-content-border text-text-primary text-xs font-semibold rounded-lg px-2.5 py-1 h-7 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-2xs">
                                    <span>{{ $entries->perPage() }}</span>
                                    <svg class="size-3 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180 text-primary' : ''" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div x-show="open" x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute bottom-full right-0 mb-1 z-[100] min-w-[5.5rem] rounded-xl border border-gray-200 bg-white shadow-xl p-1 space-y-0.5"
                            >
                                @foreach([10, 25, 50, 100] as $n)
                                    <a href="{{ request()->fullUrlWithQuery(['per_page' => $n, 'page' => 1]) }}"
                                        class="flex w-full items-center justify-between px-2.5 py-1.5 text-xs rounded-lg no-underline transition-colors {{ $entries->perPage() == $n ? 'bg-primary/10 text-primary font-bold' : 'text-text-primary hover:bg-gray-100' }}"
                                    >
                                        <span>{{ $n }}</span>
                                        @if($entries->perPage() == $n)
                                            <span class="font-bold text-primary">✓</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </footer>
                @endif
            @endif
        </div>
    </div>

    {{-- Floating Bulk Action Bar --}}
    <div x-show="selectedEntries.length > 0"
        x-cloak
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        @keydown.escape.window="selectedEntries = []"
        class="sticky bottom-6 mx-auto w-fit z-[9999] flex items-center bg-white/95 backdrop-blur-md border border-content-border shadow-md rounded-xl overflow-hidden divide-x divide-content-border text-xs font-medium"
    >
        {{-- Deselect Segment --}}
        <button type="button"
            @click="selectedEntries = []"
            class="flex items-center gap-2 px-4 py-2.5 text-blue-600 hover:bg-blue-50/80 hover:text-blue-700 transition-colors duration-150 cursor-pointer group"
        >
            <span>Deselect <span x-text="selectedEntries.length"></span> item<span x-show="selectedEntries.length > 1">s</span></span>
            <span class="bg-blue-100/80 group-hover:bg-blue-200/80 text-blue-700 text-[10px] font-bold px-1.5 py-0.5 rounded tracking-wide uppercase transition-colors">ESC</span>
        </button>

        {{-- Duplicate Segment --}}
        <form method="POST" action="{{ route('admin.forms.entries.bulk-duplicate', $form) }}" class="mb-0 flex items-center">
            @csrf
            <template x-for="id in selectedEntries" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
            <button type="submit"
                class="flex items-center gap-2 px-4 py-2.5 text-text-primary hover:bg-gray-100/80 transition-colors duration-150 cursor-pointer group"
            >
                <span>Duplicate</span>
                <span class="bg-gray-100 group-hover:bg-gray-200 text-text-muted text-[10px] font-bold px-1.5 py-0.5 rounded tracking-wide uppercase transition-colors">D</span>
            </button>
        </form>

        {{-- Delete Segment --}}
        <form id="bulk-delete-form" method="POST" action="{{ route('admin.forms.entries.bulk-destroy', $form) }}" class="mb-0 flex items-center">
            @csrf
            @method('DELETE')
            <template x-for="id in selectedEntries" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
            <button type="button"
                @click="showBulkDeleteModal = true"
                class="flex items-center gap-2 px-4 py-2.5 text-red-600 hover:bg-red-50/90 hover:text-red-700 transition-colors duration-150 cursor-pointer group"
            >
                <span>Delete</span>
                <span class="inline-flex items-center justify-center bg-red-100/80 group-hover:bg-red-200/80 text-red-700 p-1 rounded transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3 text-red-600">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                    </svg>
                </span>
            </button>
        </form>
    </div>

    {{-- Single Entry Delete Modal --}}
    <x-admin::delete-modal
        show="deletingEntry"
        title="Delete Submission"
        confirm-action="confirmDeleteEntry()"
    >
        Are you sure you want to delete this submission? This action cannot be undone.
    </x-admin::delete-modal>

    {{-- Bulk Entries Delete Modal --}}
    <x-admin::delete-modal
        show="showBulkDeleteModal"
        title="Delete Submissions"
        confirm-action="confirmBulkDelete()"
    >
        Are you sure you want to delete <span class="font-medium text-text-heading" x-text="selectedEntries.length"></span> selected submission<span x-show="selectedEntries.length > 1">s</span>? This action cannot be undone.
    </x-admin::delete-modal>
</div>

{{-- Entry detail modal --}}
<div x-data="{ entry: null, open: false, fieldLabels: {{ json_encode(collect($fields)->pluck('label', 'name')) }} }"
    @open-entry-detail.window="open = true; entry = await (await fetch('{{ route('admin.forms.entries', $form) }}/' + $event.detail.id)).json()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    @keydown.escape.window="open = false"
>
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[80vh] overflow-y-auto" @click.outside="open = false">
        <div class="flex items-center justify-between px-5 py-4 border-b border-content-border">
            <h3 class="text-base font-semibold text-text-heading">Entry #<span x-text="entry?.id"></span></h3>
            <button @click="open = false" class="p-1 text-text-muted hover:text-text-primary transition-colors">
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 1-1.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
        <div class="p-5 space-y-4">
            <template x-if="entry">
                <div>
                    <div class="mb-4 pb-4 border-b border-content-border">
                        <span class="text-xs text-text-muted">Submitted</span>
                        <p class="text-sm font-medium text-text-primary mt-0.5" x-text="new Date(entry.created_at).toLocaleString()"></p>
                    </div>
                    <template x-for="(value, key) in entry.data" :key="key">
                        <div class="mb-3">
                            <span class="text-xs text-text-muted block" x-text="fieldLabels[key] || key"></span>
                            <p class="text-sm font-medium text-text-primary mt-0.5 break-words" x-text="value || '(empty)'"></p>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- Entry edit modal --}}
<div x-data="{ 
        entry: null, 
        open: false, 
        formData: {},
        schemaFields: {{ json_encode($form->fields ?? []) }} 
    }"
    @open-entry-edit.window="
        open = true; 
        entry = await (await fetch('{{ route('admin.forms.entries', $form) }}/' + $event.detail.id)).json();
        formData = Object.assign({}, entry.data || {});
    "
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    @keydown.escape.window="open = false"
>
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 max-h-[85vh] flex flex-col" @click.outside="open = false">
        <div class="flex items-center justify-between px-5 py-4 border-b border-content-border shrink-0">
            <h3 class="text-base font-semibold text-text-heading">Edit Submission #<span x-text="entry?.id"></span></h3>
            <button @click="open = false" class="p-1 text-text-muted hover:text-text-primary transition-colors">
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 1-1.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
        <form :action="'{{ route('admin.forms.entries', $form) }}/' + entry?.id" method="POST" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            @method('PUT')
            <div class="p-5 space-y-4 overflow-y-auto flex-1">
                <template x-for="field in schemaFields" :key="field.name">
                    <div>
                        <label class="block text-xs font-medium text-text-heading mb-1.5">
                            <span x-text="field.column_name || field.label || field.name"></span>
                            <template x-if="field.required">
                                <span class="text-red-600">*</span>
                            </template>
                        </label>
                        <template x-if="field.type === 'textarea'">
                            <textarea 
                                :name="'data[' + field.name + ']'" 
                                x-model="formData[field.name]" 
                                :required="field.required"
                                rows="3"
                                class="w-full rounded-lg border border-content-border bg-content-bg p-2.5 text-xs text-text-heading focus:outline-none focus:ring-2 focus:ring-primary/20 shadow-sm"
                            ></textarea>
                        </template>
                        <template x-if="field.type === 'select'">
                            <select
                                :name="'data[' + field.name + ']'"
                                x-model="formData[field.name]"
                                :required="field.required"
                                class="w-full rounded-lg border border-content-border bg-content-bg px-3 py-2 text-xs text-text-heading focus:outline-none focus:ring-2 focus:ring-primary/20 shadow-sm"
                            >
                                <option value="">Select an option...</option>
                                <template x-for="opt in (field.options || [])" :key="opt">
                                    <option :value="opt" x-text="opt"></option>
                                </template>
                            </select>
                        </template>
                        <template x-if="!['textarea', 'select'].includes(field.type)">
                            <input 
                                :type="field.type === 'number' ? 'number' : (field.type === 'email' ? 'email' : (field.type === 'date' ? 'date' : (field.type === 'time' ? 'time' : 'text')))" 
                                :name="'data[' + field.name + ']'" 
                                x-model="formData[field.name]" 
                                :required="field.required"
                                class="w-full rounded-lg border border-content-border bg-content-bg px-3 py-2 text-xs text-text-heading focus:outline-none focus:ring-2 focus:ring-primary/20 shadow-sm"
                            >
                        </template>
                    </div>
                </template>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-content-border bg-gray-50/50 shrink-0 rounded-b-2xl">
                <button type="button" @click="open = false" class="px-4 py-2 text-xs font-medium text-text-muted hover:text-text-heading transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-primary hover:opacity-90 rounded-lg shadow-sm transition-colors cursor-pointer">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function formEntriesPage() {
        const defaultVisibility = {
            created: true,
            actions: true,
        };
        @foreach ($fields as $field)
            defaultVisibility['{{ $field['name'] }}'] = true;
        @endforeach

        const savedVis = @js($savedColumns ?? null);
        const initialVisibility = (savedVis && typeof savedVis === 'object')
            ? Object.assign(defaultVisibility, savedVis)
            : defaultVisibility;

        return {
            search: '',
            filterColumn: 'all',
            sortColumn: 'created',
            sortDirection: 'desc',
            visibleColumns: initialVisibility,
            fields: @js($fields->pluck('name')->toArray()),
            fieldLabels: @js($fields->pluck('label', 'name')->toArray()),
            selectedEntries: [],
            allEntryIds: @js($entries->pluck('id')->toArray()),
            deletingEntry: null,
            showBulkDeleteModal: false,

            confirmDeleteEntry() {
                if (!this.deletingEntry) return;
                const form = document.getElementById('delete-entry-form-' + this.deletingEntry);
                if (form) {
                    form.submit();
                }
            },

            confirmBulkDelete() {
                const form = document.getElementById('bulk-delete-form');
                if (form) {
                    form.submit();
                }
            },

            get allSelected() {
                return this.allEntryIds.length > 0 && this.selectedEntries.length === this.allEntryIds.length;
            },

            toggleSelectAll(e) {
                if (e.target.checked) {
                    this.selectedEntries = [...this.allEntryIds];
                } else {
                    this.selectedEntries = [];
                }
            },

            async saveColumnPreferences() {
                const token = document.querySelector('meta[name="csrf-token"]')?.content
                    || document.querySelector('[name=csrf-token]')?.content || '';
                try {
                    await fetch(@js(route('admin.forms.save-columns', $form)), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ columns: this.visibleColumns }),
                    });
                } catch (e) {
                    console.error('Failed to save column preferences', e);
                }
            },

            get filterColumnLabel() {
                if (this.filterColumn === 'all') return 'Filter: All';
                const label = this.fieldLabels[this.filterColumn];
                return 'Filter: ' + (label || this.filterColumn);
            },

            matchesSearch(entryData, entryId, createdAt) {
                if (!this.search.trim()) return true;
                const q = this.search.toLowerCase().trim();

                if (this.filterColumn === 'all') {
                    if (String(entryId).includes(q)) return true;
                    if (String(createdAt).toLowerCase().includes(q)) return true;
                    return Object.values(entryData || {}).some(val => {
                        if (val === null || val === undefined) return false;
                        if (Array.isArray(val)) return val.join(' ').toLowerCase().includes(q);
                        return String(val).toLowerCase().includes(q);
                    });
                }

                const targetVal = entryData ? entryData[this.filterColumn] : null;
                if (targetVal === null || targetVal === undefined) return false;
                if (Array.isArray(targetVal)) return targetVal.join(' ').toLowerCase().includes(q);
                return String(targetVal).toLowerCase().includes(q);
            },

            sortRows() {
                const tbody = this.$refs.tbody;
                if (!tbody) return;
                const rows = Array.from(tbody.querySelectorAll('tr[data-sortable]'));
                const dir = this.sortDirection === 'asc' ? 1 : -1;

                rows.sort((a, b) => {
                    let aVal, bVal;
                    if (this.sortColumn === 'id') {
                        aVal = parseInt(a.dataset.id || '0', 10);
                        bVal = parseInt(b.dataset.id || '0', 10);
                    } else if (this.sortColumn === 'created') {
                        aVal = parseInt(a.dataset.created || '0', 10);
                        bVal = parseInt(b.dataset.created || '0', 10);
                    } else {
                        aVal = (a.dataset['field' + this.sortColumn.charAt(0).toUpperCase() + this.sortColumn.slice(1)] || a.dataset['field_' + this.sortColumn] || '').toLowerCase();
                        bVal = (b.dataset['field' + this.sortColumn.charAt(0).toUpperCase() + this.sortColumn.slice(1)] || b.dataset['field_' + this.sortColumn] || '').toLowerCase();
                    }
                    return aVal < bVal ? -dir : aVal > bVal ? dir : 0;
                });

                rows.forEach(row => tbody.appendChild(row));
            }
        };
    }
</script>
@endpush

