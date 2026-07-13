@props([
    'title',
    'items',
    'sortableId',
    'dataKey',
    'reorderRoute',
    'editRoute',
    'deleteRoute',
    'emptyText',
    'emptyLinkText' => null,
    'emptyLinkRoute' => null,
    'showRoute' => false,
    'showSettings' => false,
    'settingsRoute' => null,
    'routeMethod' => 'route',
    'protectedItems' => [],
    'badgeField' => null,
    'badgeValue' => null,
    'badgeLabel' => null,
])

<div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
    <div class="px-[18px] py-3 text-sm font-medium text-text-heading flex items-center justify-between">
        <div>{{ $title }}</div>
    </div>
    <div class="px-1.5 pb-2" id="{{ $sortableId }}">
        @if ($items->isEmpty())
            <div class="flex flex-col items-center justify-center py-8">
                <p class="text-sm font-medium text-text-heading">{{ $emptyText }}</p>
                @if ($emptyLinkRoute && $emptyLinkText)
                    <p class="text-sm text-text-muted mt-1">
                        <a href="{{ route($emptyLinkRoute) }}" class="text-primary hover:text-primary/80 no-underline font-medium">{{ $emptyLinkText }}</a>
                    </p>
                @endif
            </div>
        @else
            @foreach ($items as $item)
                <div
                    class="flex rounded-xl shadow-sm bg-content-bg mb-px group px-3"
                    data-{{ $dataKey }}="{{ $item->id }}"
                >
                    <div class="w-6 shrink-0 flex items-center justify-center text-text-muted/70 cursor-grab" data-drag-handle>
                        <svg viewBox="0 0 24 24" fill="currentColor" class="size-[14px]">
                            <circle cx="8" cy="6" r="2.5" />
                            <circle cx="16" cy="6" r="2.5" />
                            <circle cx="8" cy="12" r="2.5" />
                            <circle cx="16" cy="12" r="2.5" />
                            <circle cx="8" cy="18" r="2.5" />
                            <circle cx="16" cy="18" r="2.5" />
                        </svg>
                    </div>
                    <div class="flex flex-1 items-center px-1.5 text-xs leading-normal min-w-0">
                        <div class="flex gap-2 sm:gap-3 grow items-center py-3 min-w-0">
                            <a href="{{ route($editRoute, $item) }}" class="flex items-center gap-2 no-underline min-w-0">
                                <span class="inline-block w-2 h-2 rounded-full shrink-0 {{ $item->published ? 'bg-success' : 'bg-text-muted' }}"></span>
                                <span class="text-sm font-semibold text-text-heading truncate group-hover:text-primary transition-colors">
                                    {{ $item->title }}
                                    @if ($badgeField && ($item->{$badgeField} ?? null) === $badgeValue)
                                        <span class="text-xs text-text-muted font-normal ml-1">{{ $badgeLabel }}</span>
                                    @endif
                                </span>
                            </a>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                            @if ($showRoute && method_exists($item, $routeMethod))
                                <code class="text-xs bg-panel-bg px-2 py-0.5 rounded shrink-0 text-text-muted">{{ $item->{$routeMethod}() }}</code>
                            @endif
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                                <button
                                    type="button"
                                    aria-haspopup="menu"
                                    :aria-expanded="open"
                                    aria-label="Open menu"
                                    @click="open = !open"
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-transparent text-text-primary/60 hover:bg-text-primary/10 hover:text-text-primary transition-colors"
                                >
                                    <svg viewBox="0 0 16 3" class="size-4" fill="currentColor" aria-hidden="true">
                                        <circle cx="2" cy="1.5" r="1.5" />
                                        <circle cx="8" cy="1.5" r="1.5" />
                                        <circle cx="14" cy="1.5" r="1.5" />
                                    </svg>
                                </button>
                                <div
                                    x-show="open"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    role="menu"
                                    style="z-index: 9999;"
                                    class="absolute right-0 top-full mt-1 min-w-[12rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5"
                                >
                                    <a href="{{ route($editRoute, $item) }}" role="menuitem"
                                        class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                        <span>Edit</span>
                                    </a>
                                    @if ($showSettings && $settingsRoute)
                                        <a href="{{ route($settingsRoute, $item) }}" role="menuitem"
                                            class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                                <circle cx="12" cy="12" r="3" />
                                                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />
                                            </svg>
                                            <span>Settings</span>
                                        </a>
                                    @endif
                                    @if (!in_array($item->slug ?? $item->id, $protectedItems))
                                        <hr class="my-1 border-content-border">
                                        <form method="POST" action="{{ route($deleteRoute, $item) }}" class="w-full">
                                            @csrf @method('DELETE')
                                            <button type="submit" role="menuitem"
                                                onclick="return confirm('Delete this item?')"
                                                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-red-500">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                                                </svg>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
