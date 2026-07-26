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

    $headers = [
        ['key' => 'id', 'label' => '#'],
    ];
    foreach ($fields as $field) {
        $headers[] = ['key' => $field['name'], 'label' => $field['label']];
    }
    $headers[] = ['key' => 'created', 'label' => 'Submitted'];
    $headers[] = ['key' => 'actions', 'label' => 'Actions'];
@endphp

<div class="max-w-5xl mx-auto px-2 sm:px-0" x-data="formEntriesPage()">
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                <line x1="3" y1="9" x2="21" y2="9" />
                <line x1="9" y1="21" x2="9" y2="9" />
            </svg>
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
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
            >
                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path d="M10 1a1 1 0 011 1v9.586l2.293-2.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 11.586V2a1 1 0 011-1z" />
                    <path d="M2 16a1 1 0 011 1v1h12v-1a1 1 0 112 0v2a1 1 0 01-1 1H2a1 1 0 01-1-1v-2a1 1 0 011-1z" />
                </svg>
                <span>Export</span>
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
                            <button type="button" @click="sortColumn = 'id'; sortRows(); open = false" class="flex w-full items-center justify-between gap-2 whitespace-nowrap px-3 py-1.5 rounded-lg text-xs transition-colors cursor-pointer" :class="sortColumn === 'id' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-body-bg'">
                                <span>ID (#)</span>
                                <span x-show="sortColumn === 'id'" class="font-bold">✓</span>
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
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['id']" @change="saveColumnPreferences()" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span># (ID)</span>
                            </label>
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
            <x-admin::table
                :headers="$headers"
                :items="$entries"
                emptyText="No entries yet."
                emptySubtext="Submissions for “{{ $form->title }}” will appear here."
            >
                <tbody x-ref="tbody">
                    @foreach($entries as $entry)
                        <tr data-sortable
                            data-id="{{ $entry->id }}"
                            data-created="{{ $entry->created_at->timestamp }}"
                            @foreach($fields as $f)
                                data-field-{{ $f['name'] }}="{{ strtolower(is_array($entry->data[$f['name']] ?? null) ? implode(' ', $entry->data[$f['name']]) : (string)($entry->data[$f['name']] ?? '')) }}"
                            @endforeach
                            x-show="matchesSearch({{ json_encode($entry->data) }}, {{ $entry->id }}, {{ json_encode($entry->created_at->format('M j, Y g:i A')) }})"
                            class="group border-b border-content-border last:border-0 hover:bg-[#f9fafb] transition-colors"
                        >
                            <td x-show="visibleColumns['id'] !== false" class="px-4 py-3 text-text-muted text-xs whitespace-nowrap min-w-[70px] group-last:rounded-bl-xl">#{{ $entry->id }}</td>
                            
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
                                <td x-show="visibleColumns['{{ $field['name'] }}'] !== false" class="px-4 py-3 text-text-primary max-w-[200px] whitespace-nowrap overflow-hidden">
                                    <span class="block max-w-[200px] truncate" title="{{ is_scalar($value) ? $value : '' }}">
                                        {{ filled($value) || $value === 0 || $value === '0' ? $value : '—' }}
                                    </span>
                                </td>
                            @endforeach

                            <td x-show="visibleColumns['created'] !== false" class="px-4 py-3 text-text-primary whitespace-nowrap min-w-[160px]">
                                <span class="font-medium">{{ $entry->created_at->format('M j, Y g:i A') }}</span>
                            </td>
                            <td x-show="visibleColumns['actions'] !== false" class="sticky right-0 bg-white group-hover:bg-[#f9fafb] group-last:rounded-br-xl z-10 px-4 py-3 text-right whitespace-nowrap transition-colors">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- View Icon Button --}}
                                    <a href="#" @click.prevent="$dispatch('open-entry-detail', { id: {{ $entry->id }} })"
                                        class="size-8 inline-flex items-center justify-center rounded-lg border border-content-border bg-white text-text-muted hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-colors shadow-sm"
                                        title="View Entry"
                                        aria-label="View Entry"
                                    >
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                            <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                            <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>

                                    {{-- Edit Icon Button --}}
                                    <a href="#" @click.prevent="$dispatch('open-entry-edit', { id: {{ $entry->id }} })"
                                        class="size-8 inline-flex items-center justify-center rounded-lg border border-content-border bg-white text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors shadow-sm"
                                        title="Edit Entry"
                                        aria-label="Edit Entry"
                                    >
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                            <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                                            <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                                        </svg>
                                    </a>

                                    {{-- Delete Icon Button --}}
                                    <form method="POST" action="{{ route('admin.forms.entries.destroy', [$form, $entry]) }}" class="inline mb-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            onclick="return confirm('Are you sure you want to delete this submission?')"
                                            class="size-8 inline-flex items-center justify-center rounded-lg border border-content-border bg-white text-text-muted hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-colors shadow-sm cursor-pointer"
                                            title="Delete Entry"
                                            aria-label="Delete Entry"
                                        >
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 01.75.72v5.25a.75.75 0 01-1.5 0V8.44a.75.75 0 01.75-.72zm3.34 0a.75.75 0 01.75.72v5.25a.75.75 0 01-1.5 0V8.44a.75.75 0 01.75-.72z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin::table>

            @if($entries->hasPages())
                <div class="flex items-center justify-between px-4 py-3 border-t border-content-border mt-3 bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm">
                    <span class="text-xs text-text-muted">Showing {{ $entries->firstItem() }}-{{ $entries->lastItem() }} of {{ $entries->total() }}</span>
                    <div class="flex items-center gap-1">
                        @if($entries->onFirstPage())
                            <span class="px-2 py-1 text-xs text-text-muted/40">Prev</span>
                        @else
                            <a href="{{ $entries->previousPageUrl() }}" class="px-2 py-1 text-xs text-text-muted hover:text-text-primary transition-colors">Prev</a>
                        @endif
                        @if($entries->hasMorePages())
                            <a href="{{ $entries->nextPageUrl() }}" class="px-2 py-1 text-xs text-text-muted hover:text-text-primary transition-colors">Next</a>
                        @else
                            <span class="px-2 py-1 text-xs text-text-muted/40">Next</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
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
                        <label class="block text-xs font-medium text-text-heading mb-1.5" x-text="field.column_name || field.label || field.name"></label>
                        <template x-if="field.type === 'textarea'">
                            <textarea 
                                :name="'data[' + field.name + ']'" 
                                x-model="formData[field.name]" 
                                rows="3"
                                class="w-full rounded-lg border border-content-border bg-content-bg p-2.5 text-xs text-text-heading focus:outline-none focus:ring-2 focus:ring-primary/20 shadow-sm"
                            ></textarea>
                        </template>
                        <template x-if="field.type !== 'textarea'">
                            <input 
                                :type="field.type === 'number' ? 'number' : (field.type === 'email' ? 'email' : 'text')" 
                                :name="'data[' + field.name + ']'" 
                                x-model="formData[field.name]" 
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
            id: true,
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

