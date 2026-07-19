<div
    x-data="{
        selectedFormId: {{ $selectedFormId ? (int) $selectedFormId : 'null' }},
        saving: false,
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
        formatValue(value) {
            if (value === null || value === undefined || value === '') return '—';
            if (Array.isArray(value)) return value.join(', ');
            if (typeof value === 'object') return JSON.stringify(value);
            return String(value);
        },
    }"
    class="space-y-3"
>
    <div class="flex flex-wrap items-center justify-between gap-3 px-1">
        <div class="flex items-center gap-2 min-w-0">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-text-muted shrink-0">
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <path d="M3 9h18M9 21V9" />
            </svg>
            <div class="min-w-0">
                <div class="text-[14px] font-medium text-text-heading truncate">
                    {{ $form?->title ?? 'Form Entries' }}
                </div>
                <div class="text-[12px] text-text-muted">
                    Latest submissions
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if ($forms->isNotEmpty())
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex h-8 items-center gap-2 rounded-lg border border-content-border bg-white px-3 text-[12px] font-medium text-text-heading shadow-sm hover:bg-gray-50 cursor-pointer"
                        :class="saving ? 'opacity-60 pointer-events-none' : ''"
                    >
                        <span class="max-w-[180px] truncate">{{ $form?->title ?? 'Select form' }}</span>
                        <svg class="size-3.5 text-text-muted shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div
                        x-show="open"
                        x-cloak
                        x-transition
                        class="absolute right-0 z-50 top-full mt-1 min-w-[200px] max-h-64 overflow-y-auto bg-content-bg border border-content-border rounded-lg shadow-lg p-1"
                    >
                        @foreach ($forms as $option)
                            <button
                                type="button"
                                @click="open = false; selectForm({{ $option->id }})"
                                class="w-full text-left px-3 py-2 text-[12px] rounded-md transition-colors {{ (int) $selectedFormId === (int) $option->id ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30' }}"
                            >
                                {{ $option->title }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($form)
                <a
                    href="{{ route('admin.forms.entries', $form) }}"
                    class="flex h-8 items-center gap-1.5 rounded-lg border border-content-border bg-white px-3 text-[12px] font-medium text-text-heading shadow-sm hover:bg-gray-50 no-underline"
                >
                    View all
                </a>
            @endif
        </div>
    </div>

    @if ($forms->isEmpty())
        <div class="rounded-xl border border-dashed border-content-border bg-gray-50/60 px-6 py-12 text-center">
            <p class="text-sm font-medium text-text-heading">No forms yet</p>
            <p class="mt-1 text-xs text-text-muted">Create a form to show its submissions here.</p>
            <a href="{{ route('admin.forms.create') }}" class="mt-4 inline-flex h-8 items-center rounded-lg bg-primary px-3 text-[12px] font-medium text-white no-underline hover:opacity-90">
                Create form
            </a>
        </div>
    @else
        @php
            $headers = ['#'];
            foreach ($fields as $field) {
                $headers[] = $field['label'] ?? $field['name'];
            }
            $headers[] = 'Submitted';
            $headers[] = 'Actions';
        @endphp

        <x-admin::table
            :headers="$headers"
            :items="$entries"
            emptyText="No entries yet."
            emptySubtext="Submissions for “{{ $form->title }}” will appear here."
        >
            @foreach ($entries as $entry)
                <tr class="group transition-colors hover:bg-gray-50/50">
                    <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 font-medium text-gray-900">
                        #{{ $entry->id }}
                    </td>
                    @foreach ($fields as $field)
                        @php
                            $value = $entry->data[$field['name']] ?? null;
                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            } elseif (is_bool($value)) {
                                $value = $value ? 'Yes' : 'No';
                            }
                        @endphp
                        <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 text-gray-900 max-w-[220px] truncate" title="{{ is_scalar($value) ? $value : '' }}">
                            {{ filled($value) || $value === 0 || $value === '0' ? $value : '—' }}
                        </td>
                    @endforeach
                    <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 text-gray-400">
                        {{ $entry->created_at?->format('M j, Y g:i A') }}
                    </td>
                    <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 text-right">
                        <a
                            href="{{ route('admin.forms.entries', $form) }}"
                            class="text-[12px] font-medium text-primary hover:text-primary/80 no-underline"
                        >
                            View
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-admin::table>
    @endif
</div>
