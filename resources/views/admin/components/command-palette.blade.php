{{--
  Command palette — global admin search / jump-to.
  Opens via header Search button or ⌘K / Ctrl+K.
  Fetches ranked results from admin.search; keyboard: ↑/↓, Enter, Esc.
--}}
<div
    x-data="commandPalette()"
    x-cloak
    @open-command-palette.window="openPalette()"
    @keydown.window="onGlobalKey($event)"
>
    <template x-if="open">
        <div class="fixed inset-0 z-[100] flex items-start justify-center pt-[14vh] pb-[12vh] px-4" role="dialog" aria-modal="true" aria-label="Command palette">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="close()"></div>

            <div
                class="relative w-full max-w-xl overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black/10"
                @keydown="onKeyDown($event)"
            >
                <div class="flex items-center gap-2.5 border-b border-gray-200 px-3.5">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="w-[18px] h-[18px] shrink-0 text-text-muted">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                    <input
                        x-ref="input"
                        type="text"
                        x-model="query"
                        @input="onQuery()"
                        placeholder="Search or jump to…"
                        class="w-full border-0 bg-transparent py-3.5 text-sm text-text-primary outline-none placeholder:text-text-muted focus:border-0 focus:ring-0 focus:shadow-none"
                        autocomplete="off"
                        spellcheck="false"
                    />
                    <span x-show="loading" class="text-[11px] text-text-muted shrink-0">…</span>
                </div>

                <div class="max-h-[60vh] overflow-y-auto py-2" x-ref="list">
                    <p x-show="!loading && flat.length === 0" class="px-4 py-8 text-center text-sm text-text-muted">
                        No results for “<span x-text="query"></span>”.
                    </p>

                    <template x-for="(g, gi) in groups" :key="g.group">
                        <div class="mb-1">
                            <div class="px-4 pb-1 pt-2 text-[11px] font-semibold uppercase tracking-wider text-text-muted" x-text="g.group"></div>
                            <template x-for="(cmd, ii) in g.items" :key="cmd.id">
                                <button
                                    type="button"
                                    @click="select(cmd)"
                                    @mousemove="setActiveFromCmd(cmd)"
                                    :data-cmd-id="cmd.id"
                                    :class="isActive(cmd) ? 'bg-primary/10' : 'hover:bg-black/[0.03]'"
                                    class="flex w-full items-center gap-3 px-4 py-2 text-left transition-colors"
                                >
                                    <span class="shrink-0 text-text-muted" x-html="groupIcon(g.group)"></span>
                                    <span class="min-w-0 grow">
                                        <span class="block truncate text-sm text-text-primary" x-text="cmd.title"></span>
                                        <span x-show="cmd.subtitle" class="block truncate text-xs text-text-muted" x-text="cmd.subtitle"></span>
                                    </span>
                                    <svg x-show="isActive(cmd)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5 shrink-0 text-text-muted">
                                        <polyline points="9 10 4 15 9 20" />
                                        <path d="M20 4v7a4 4 0 01-4 4H4" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="flex items-center gap-4 border-t border-gray-200 px-4 py-2 text-[11px] text-text-muted">
                    <span class="flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5">
                            <path d="M7 15l5 5 5-5M7 9l5-5 5 5" />
                        </svg>
                        Navigate
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5">
                            <polyline points="9 10 4 15 9 20" />
                            <path d="M20 4v7a4 4 0 01-4 4H4" />
                        </svg>
                        Select
                    </span>
                    <span class="ml-auto flex items-center gap-1.5">
                        <kbd class="rounded bg-gray-100 px-1.5 py-0.5 font-medium">Esc</kbd>
                        Close
                    </span>
                </div>
            </div>
        </div>
    </template>
</div>

@once
@push('scripts')
<script>
function commandPalette() {
    const searchUrl = @json(route('admin.search'));
    const icons = {
        Pages: '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
        Blog: '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>',
        Layouts: '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>',
        Taxonomies: '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>',
        Navigation: '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>',
    };

    return {
        open: false,
        query: '',
        groups: [],
        active: 0,
        loading: false,
        _timer: null,
        _seq: 0,

        get flat() {
            return this.groups.flatMap((g) => g.items);
        },

        groupIcon(group) {
            return icons[group] || icons.Navigation;
        },

        isActive(cmd) {
            const flat = this.flat;
            return flat[this.active]?.id === cmd.id;
        },

        setActiveFromCmd(cmd) {
            const idx = this.flat.findIndex((c) => c.id === cmd.id);
            if (idx >= 0) this.active = idx;
        },

        onGlobalKey(e) {
            if ((e.metaKey || e.ctrlKey) && (e.key === 'k' || e.key === 'K')) {
                e.preventDefault();
                if (this.open) this.close();
                else this.openPalette();
            }
        },

        async openPalette() {
            this.open = true;
            this.query = '';
            this.active = 0;
            await this.search();
            this.$nextTick(() => this.$refs.input?.focus());
        },

        close() {
            this.open = false;
            this.query = '';
            this.groups = [];
            this.active = 0;
        },

        onQuery() {
            this.active = 0;
            clearTimeout(this._timer);
            this._timer = setTimeout(() => this.search(), 120);
        },

        async search() {
            const seq = ++this._seq;
            this.loading = true;
            try {
                const url = searchUrl + (this.query ? ('?q=' + encodeURIComponent(this.query)) : '');
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('Search failed');
                const data = await res.json();
                if (seq !== this._seq) return;
                this.groups = data.groups || [];
                if (this.active >= this.flat.length) this.active = Math.max(0, this.flat.length - 1);
            } catch (e) {
                if (seq !== this._seq) return;
                this.groups = [];
            } finally {
                if (seq === this._seq) this.loading = false;
            }
        },

        select(cmd) {
            if (!cmd?.href) return;
            window.location.href = cmd.href;
        },

        onKeyDown(e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.active = Math.min(this.active + 1, Math.max(0, this.flat.length - 1));
                this.scrollActive();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.active = Math.max(this.active - 1, 0);
                this.scrollActive();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                this.select(this.flat[this.active]);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                this.close();
            }
        },

        scrollActive() {
            this.$nextTick(() => {
                const cmd = this.flat[this.active];
                if (!cmd) return;
                const el = this.$root.querySelector('[data-cmd-id="' + CSS.escape(cmd.id) + '"]');
                el?.scrollIntoView({ block: 'nearest' });
            });
        },
    };
}
</script>
@endpush
@endonce
