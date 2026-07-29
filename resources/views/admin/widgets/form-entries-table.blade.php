<div
    data-form-title="{{ $form?->title ?? '' }}"
    x-data="{
        selectedFormId: {{ $selectedFormId ? (int) $selectedFormId : 'null' }},
        saving: false,
        search: '',
        filterColumn: 'all',
        sortColumn: 'created',
        sortDirection: 'desc',
        visibleColumns: {},

        init() {
            let cols = { id: true, created: true, actions: true };
            @foreach($fields as $f)
                cols['{{ $f['name'] }}'] = true;
            @endforeach
            this.visibleColumns = cols;
        },

        get filterColumnLabel() {
            if (this.filterColumn === 'all') return 'Filter: All';
            const colMap = {
                id: '#',
                created: 'Submitted Date',
                @foreach ($fields as $f)
                    '{{ $f['name'] }}': '{{ $f['label'] }}',
                @endforeach
            };
            return 'Filter: ' + (colMap[this.filterColumn] || this.filterColumn);
        },

        async selectForm(formId) {
            if (!formId || formId === this.selectedFormId || this.saving) return;
            this.saving = true;
            this.selectedFormId = formId;
            const token = document.querySelector('[name=csrf-token]')?.content || '';
            try {
                await fetch(@js(route('admin.widgets.layout')), {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                    body: JSON.stringify({
                        table: {
                            order: ['form_entries_table'],
                            hidden: [],
                            form_id: formId,
                        },
                    }),
                });
                const response = await fetch(@js(route('admin.widgets.render')), {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                    body: JSON.stringify({
                        zone: 'table',
                        type: 'form_entries_table',
                        form_id: formId,
                    }),
                });
                const data = await response.json();
                const container = document.getElementById('table-widget-container');
                if (container && data.html) {
                    container.innerHTML = data.html;
                    if (window.Alpine) {
                        Alpine.initTree(container);
                    }
                }
            } finally {
                this.saving = false;
            }
        },

        matchesSearch(data, id, dateStr) {
            if (!this.search || !this.search.trim()) return true;
            const q = this.search.toLowerCase().trim();

            if (this.filterColumn !== 'all') {
                if (this.filterColumn === 'id') return String(id).includes(q);
                if (this.filterColumn === 'created') return String(dateStr).toLowerCase().includes(q);
                const val = data[this.filterColumn];
                if (val === null || val === undefined) return false;
                if (Array.isArray(val)) return val.join(' ').toLowerCase().includes(q);
                return String(val).toLowerCase().includes(q);
            }

            if (String(id).includes(q)) return true;
            if (String(dateStr).toLowerCase().includes(q)) return true;
            for (let k in data) {
                const val = data[k];
                if (val !== null && val !== undefined) {
                    if (Array.isArray(val) && val.join(' ').toLowerCase().includes(q)) return true;
                    if (String(val).toLowerCase().includes(q)) return true;
                }
            }
            return false;
        },

        sortRows() {
            const tbody = this.$refs.tbody;
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr[data-sortable]'));
            const dir = this.sortDirection === 'asc' ? 1 : -1;
            const col = this.sortColumn;

            rows.sort((a, b) => {
                let valA, valB;
                if (col === 'id') {
                    valA = parseInt(a.dataset.id || 0);
                    valB = parseInt(b.dataset.id || 0);
                } else if (col === 'created') {
                    valA = parseInt(a.dataset.created || 0);
                    valB = parseInt(b.dataset.created || 0);
                } else {
                    valA = (a.dataset['field' + col.replace(/_/g, '').toLowerCase()] || '').toLowerCase();
                    valB = (b.dataset['field' + col.replace(/_/g, '').toLowerCase()] || '').toLowerCase();
                }
                if (valA < valB) return -1 * dir;
                if (valA > valB) return 1 * dir;
                return 0;
            });

            rows.forEach(r => tbody.appendChild(r));
        }
    }"
    class="space-y-3"
>
    @if ($forms->isEmpty())
        <div class="rounded-xl border border-dashed border-content-border bg-gray-50/60 px-6 py-12 text-center">
            <p class="text-sm font-medium text-text-heading">No forms yet</p>
            <p class="mt-1 text-xs text-text-muted">Create a form to show its submissions here.</p>
            <a href="{{ route('admin.forms.create') }}" class="mt-4 inline-flex h-8 items-center rounded-lg bg-primary px-3 text-[12px] font-medium text-white no-underline hover:opacity-90">
                Create form
            </a>
        </div>
    @elseif (!$form || $entries->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 text-center px-6 border border-content-border/60 rounded-xl bg-content-bg">
            <img src="/empty-collection.svg" alt="No items" class="size-24 mb-3 opacity-60">
            <p class="text-sm font-medium text-text-heading">No submissions yet</p>
            <p class="text-xs text-text-muted mt-1">Submissions for “{{ $form?->title ?? 'selected form' }}” will appear here.</p>
        </div>
    @else
        <div class="rounded-xl ring-1 ring-content-border bg-content-bg shadow-sm relative">
            <div class="relative z-30 flex flex-wrap items-center justify-between gap-3 px-3 py-2.5 border-b border-content-border bg-[#f9fafb] rounded-t-xl">
                {{-- Search Input (Left) --}}
                <div class="relative shrink-0">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-text-muted">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Search..."
                        aria-label="Search submissions"
                        class="h-8 w-36 sm:w-56 rounded-lg border border-content-border bg-white pl-8 pr-3 text-[12px] text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/10 shadow-sm"
                    >
                </div>

                {{-- Right Controls (Filter, Sort, Columns) --}}
                <div class="flex flex-wrap items-center gap-2 shrink-0">

                    {{-- Filter Dropdown --}}
                    <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button"
                            @click="open = !open"
                            class="flex h-8 items-center gap-1.5 whitespace-nowrap rounded-lg border border-content-border bg-white px-2.5 text-[12px] font-medium text-text-heading hover:bg-body-bg shadow-sm transition-colors cursor-pointer">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-text-muted shrink-0">
                                <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                            </svg>
                            <span x-text="filterColumnLabel" class="whitespace-nowrap">Filter: All</span>
                            <svg class="size-3 text-text-muted shrink-0 transition-transform ml-0.5" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                            class="absolute right-0 top-full mt-2 min-w-[13rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 space-y-0.5 z-[100]">
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
                            class="absolute right-0 top-full mt-2 min-w-[13rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 space-y-0.5 z-[100]">
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
                            class="absolute right-0 top-full mt-2 min-w-[14rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-2 space-y-1 z-[100]">
                            <div class="px-2 py-1 text-[11px] font-semibold text-text-muted uppercase tracking-wider">
                                Display Columns
                            </div>
                            <div class="my-1 border-t border-content-border"></div>
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['id']" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span># (ID)</span>
                            </label>
                            @foreach ($fields as $field)
                                <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                    <input type="checkbox" x-model="visibleColumns['{{ $field['name'] }}']" class="rounded border-content-border text-primary focus:ring-primary/20">
                                    <span>{{ $field['label'] }}</span>
                                </label>
                            @endforeach
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['created']" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span>Submitted Date</span>
                            </label>
                            <label class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-body-bg text-xs text-text-primary cursor-pointer select-none">
                                <input type="checkbox" x-model="visibleColumns['actions']" class="rounded border-content-border text-primary focus:ring-primary/20">
                                <span>Actions</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 overflow-x-auto table-scrollbar rounded-b-xl">
                <table class="w-full min-w-full border-separate border-spacing-y-0 text-left text-[13px]">
                    <thead>
                        <tr class="bg-[#f9fafb]">
                            <th x-show="visibleColumns['id'] !== false" class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border rounded-tl-xl">
                                <button @click="sortColumn = 'id'; sortRows()" class="cursor-pointer hover:text-text-heading">#</button>
                            </th>
                            @foreach ($fields as $field)
                                <th x-show="visibleColumns['{{ $field['name'] }}'] !== false" class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                    <button @click="sortColumn = '{{ $field['name'] }}'; sortRows()" class="cursor-pointer hover:text-text-heading">{{ $field['label'] }}</button>
                                </th>
                            @endforeach
                            <th x-show="visibleColumns['created'] !== false" class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border">
                                <button @click="sortColumn = 'created'; sortRows()" class="cursor-pointer hover:text-text-heading">Submitted</button>
                            </th>
                            <th x-show="visibleColumns['actions'] !== false" class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] border-b border-content-border text-right rounded-tr-xl">Actions</th>
                        </tr>
                    </thead>
                    <tbody x-ref="tbody">
                        @foreach ($entries as $entry)
                            <tr data-sortable
                                data-id="{{ $entry->id }}"
                                data-created="{{ $entry->created_at->timestamp }}"
                                @foreach($fields as $f)
                                    data-field-{{ $f['name'] }}="{{ strtolower(is_array($entry->data[$f['name']] ?? null) ? implode(' ', $entry->data[$f['name']]) : (string)($entry->data[$f['name']] ?? '')) }}"
                                @endforeach
                                x-show="matchesSearch({{ json_encode($entry->data) }}, {{ $entry->id }}, {{ json_encode($entry->created_at->format('M j, Y g:i A')) }})"
                                class="group hover:bg-[#f9fafb] transition-colors"
                            >
                                <td x-show="visibleColumns['id'] !== false" class="px-4 py-3 text-text-muted text-xs whitespace-nowrap min-w-[70px] border-b border-content-border group-last:border-b-0 group-last:rounded-bl-xl">#{{ $entry->id }}</td>

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
                                    <td x-show="visibleColumns['{{ $field['name'] }}'] !== false" class="px-4 py-3 text-text-primary max-w-[200px] whitespace-nowrap overflow-hidden border-b border-content-border group-last:border-b-0">
                                        <span class="block max-w-[200px] truncate" title="{{ is_scalar($value) ? $value : '' }}">
                                            {{ filled($value) || $value === 0 || $value === '0' ? $value : '—' }}
                                        </span>
                                    </td>
                                @endforeach

                                <td x-show="visibleColumns['created'] !== false" class="px-4 py-3 text-text-primary whitespace-nowrap min-w-[160px] border-b border-content-border group-last:border-b-0">
                                    <span class="font-medium">{{ $entry->created_at->format('M j, Y g:i A') }}</span>
                                </td>
                                <td x-show="visibleColumns['actions'] !== false" class="group-last:rounded-br-xl px-4 py-3 text-right whitespace-nowrap transition-colors border-b border-content-border group-last:border-b-0">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.forms.entries', $form) }}"
                                            class="size-8 inline-flex items-center justify-center rounded-lg border border-content-border bg-white text-text-muted hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-colors shadow-sm"
                                            title="View Entry"
                                            aria-label="View Entry"
                                        >
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                                <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
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
                                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 01.75.72v5.25a.75.75 0 01-1.5 0V8.44a.75.75 0 01.75-.72zm3.34 0a.75.75 0 01.75.72v5.25a.75.75 0 01-1.5 0V8.44a.75.75 0 01.75-.72z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
