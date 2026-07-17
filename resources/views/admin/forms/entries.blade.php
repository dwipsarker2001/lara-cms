@extends('admin.layout')

@section('title', $form->title . ' — Entries')
@section('breadcrumb', 'Form Entries')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0">
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
            <a href="{{ route('admin.forms.index') }}"
                class="size-10 flex items-center justify-center rounded-lg border border-content-border bg-white text-text-primary hover:bg-gray-50 transition-colors shadow-sm"
                title="All forms"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-grid-3x3">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
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
            @if($entries->total() > 0)
                <span class="text-sm text-text-muted">{{ $entries->total() }} entries</span>
            @endif
        </div>
    </header>

<div class="bg-panel-bg rounded-2xl p-[7px]">
    @if($entries->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center px-6">
            <img src="/empty-collection.svg" alt="No items" class="size-32 mb-4 opacity-60">
            <p class="text-sm font-medium text-text-heading">No entries yet.</p>
            <p class="text-xs text-text-muted mt-1">Entries will appear here once the form is submitted.</p>
        </div>
        @else
            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-content-border">
                            <th class="text-left text-xs font-semibold text-text-muted uppercase tracking-wider px-4 py-3">#</th>
                            <th class="text-left text-xs font-semibold text-text-muted uppercase tracking-wider px-4 py-3">Submitted</th>
                            <th class="text-right text-xs font-semibold text-text-muted uppercase tracking-wider px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $entry)
                            <tr class="border-b border-content-border last:border-0 hover:bg-body-bg/50 transition-colors">
                                <td class="px-4 py-3 text-text-muted text-xs">#{{ $entry->id }}</td>
                                <td class="px-4 py-3 text-text-primary">
                                    <span class="font-medium">{{ $entry->created_at->format('M j, Y g:i A') }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="#" @click.prevent="$dispatch('open-entry-detail', { id: {{ $entry->id }} })"
                                        class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary/80 transition-colors"
                                    >
                                        View
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                            <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                            <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($entries->hasPages())
                <div class="flex items-center justify-between px-4 py-3 border-t border-content-border">
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
        @endif
    </div>
</div>

{{-- Entry detail modal --}}
<div x-data="{ entry: null, open: false }"
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
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
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
                            <span class="text-xs text-text-muted block" x-text="key"></span>
                            <p class="text-sm font-medium text-text-primary mt-0.5 break-words" x-text="value || '(empty)'"></p>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>
</div>
@endsection
