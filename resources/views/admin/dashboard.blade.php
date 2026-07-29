@extends('admin.layout')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<script>
    window._dashboardData = {
        gridShow: @json(collect(range(0, count($gridWidgets) - 1))->mapWithKeys(fn ($i) => [$i => !in_array($i, $gridHidden)])->all()),
        gridOrder: @json(range(0, count($gridWidgets) - 1)),
        gridWidgets: @json($gridWidgetList),
        chartShow: @json(collect(range(0, count($chartWidgets) - 1))->mapWithKeys(fn ($i) => [$i => !in_array($i, $chartHidden)])->all()),
        chartWidgets: @json($chartWidgetList),
        tableShow: @json(collect(range(0, count($tableWidgets) - 1))->mapWithKeys(fn ($i) => [$i => !in_array($i, $tableHidden)])->all()),
        tableWidgets: @json($tableWidgetList),
        listShow: @json(collect(range(0, count($listWidgets) - 1))->mapWithKeys(fn ($i) => [$i => !in_array($i, $listHidden)])->all()),
        listWidgets: @json($listWidgetList),
        allByZone: @json($allByZone),
        tableForms: @json($sidebarForms->values()),
    };
    function dashboard() {
        return {
            period: 'Today',
            selected: 'Today',
            editing: false,
            gridShow: window._dashboardData.gridShow,
            gridOrder: window._dashboardData.gridOrder,
            gridWidgets: window._dashboardData.gridWidgets,
            chartShow: window._dashboardData.chartShow,
            chartWidgets: window._dashboardData.chartWidgets,
            tableShow: window._dashboardData.tableShow,
            tableWidgets: window._dashboardData.tableWidgets,
            listShow: window._dashboardData.listShow,
            listWidgets: window._dashboardData.listWidgets,
            allByZone: window._dashboardData.allByZone,
            tableForms: window._dashboardData.tableForms,
            selectedFormId: null,
            dragIdx: null,
            clickedSlot: null,
            panelOpen: false,
            panelClosing: false,
            dragStart(evt, idx) {
                this.dragIdx = idx;
                evt.target.classList.add('opacity-40', 'ring-2', 'ring-primary/30', 'ring-inset');
            },
            allowDrop(evt) {
                evt.preventDefault();
            },
            drop(evt, idx) {
                evt.preventDefault();
                if (this.dragIdx === null || this.dragIdx === idx) return;
                const from = this.gridOrder.indexOf(this.dragIdx);
                const to = this.gridOrder.indexOf(idx);
                const order = [...this.gridOrder];
                const [removed] = order.splice(from, 1);
                order.splice(to, 0, removed);
                this.gridOrder = order;
                this.dragIdx = null;
            },
            dragEnd(evt) {
                evt.target.classList.remove('opacity-40', 'ring-2', 'ring-primary/30', 'ring-inset');
                this.dragIdx = null;
            },
            openPanel() {
                this.panelOpen = true;
            },
            closePanel() {
                this.panelClosing = true;
                setTimeout(() => {
                    this.panelClosing = false;
                    this.panelOpen = false;
                }, 200);
            },
            addWidget(idx) {
                if (typeof this.clickedSlot === 'number' && this.clickedSlot !== idx) {
                    const from = this.gridOrder.indexOf(idx);
                    const to = this.gridOrder.indexOf(this.clickedSlot);
                    if (from !== -1 && to !== -1) {
                        const order = [...this.gridOrder];
                        const [removed] = order.splice(from, 1);
                        order.splice(to, 0, removed);
                        this.gridOrder = order;
                    }
                }
                let payload = {grid: {order: this.gridOrder.map(i => this.gridWidgets[i]?.type), hidden: Object.keys(this.gridShow).filter(i => !this.gridShow[i] && i != idx).map(Number)}};
                fetch('/admin/widgets/layout', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]')?.content || ''},
                    body: JSON.stringify(payload),
                }).then(() => location.reload());
            },
            selectZoneWidget(zone, type) {
                let payload = {};
                if (zone === 'grid') {
                    payload.grid = {order: this.gridOrder.map(i => this.gridWidgets[i]?.type), hidden: Object.keys(this.gridShow).filter(i => !this.gridShow[i]).map(Number)};
                    fetch('/admin/widgets/layout', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]')?.content || ''},
                        body: JSON.stringify(payload),
                    }).then(() => location.reload());
                } else {
                    let savePayload = {order: [type], hidden: []};
                    let renderPayload = {zone, type};
                    if (zone === 'table' && this.selectedFormId) {
                        savePayload.form_id = this.selectedFormId;
                        renderPayload.form_id = this.selectedFormId;
                    }
                    payload[zone] = savePayload;
                    fetch('/admin/widgets/layout', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]')?.content || ''},
                        body: JSON.stringify(payload),
                    }).then(() => {
                        fetch('/admin/widgets/render', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]')?.content || ''},
                            body: JSON.stringify(renderPayload),
                        })
                        .then(r => r.json())
                        .then(data => {
                            document.getElementById(zone + '-widget-container').innerHTML = data.html;
                            if (window.Alpine) Alpine.initTree(document.getElementById(zone + '-widget-container'));
                            this[zone + 'Widgets'] = [{index: 0, type: data.type, label: data.label, image: data.image || null}];
                            this[zone + 'Show'][0] = true;
                            this.closePanel();
                        });
                    });
                }
            },
            toggleZoneWidget(zone, idx, hidden) {
                let currentType = this[zone + 'Widgets'][idx]?.type;
                let payload = {};
                payload[zone] = {order: currentType ? [currentType] : [], hidden: hidden ? [idx] : []};
                fetch('/admin/widgets/layout', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]')?.content || ''},
                    body: JSON.stringify(payload),
                });
                this[zone + 'Show'][idx] = !hidden;
            },
            saveLayout() {
                let payload = {
                    grid: {order: this.gridOrder.map(i => this.gridWidgets[i]?.type), hidden: Object.keys(this.gridShow).filter(i => !this.gridShow[i]).map(Number)},
                    chart: {order: [this.chartWidgets[0]?.type].filter(Boolean), hidden: !this.chartShow[0] ? [0] : []},
                    table: {order: [this.tableWidgets[0]?.type].filter(Boolean), hidden: !this.tableShow[0] ? [0] : []},
                    list: {order: [this.listWidgets[0]?.type].filter(Boolean), hidden: !this.listShow[0] ? [0] : []},
                };
                fetch('/admin/widgets/layout', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]')?.content || ''},
                    body: JSON.stringify(payload),
                });
                this.editing = false;
            },
            gridZoneWidgets() {
                if (!this.allByZone?.grid) return [];
                return this.allByZone.grid.filter(w => !this.gridShow[this.gridWidgets.findIndex(g => g?.type === w.type)]);
            },
        };
    }
</script>
    <div x-data="dashboard()">
        <header class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-[25px] font-semibold text-text-heading">Hello, {{ Auth::guard('admin')->user()->name }} 👋</h1>
                <p class="mt-1 text-[14px] text-text-muted">Here are the latest insights from your customer interactions.</p>
            </div>
            <div class="flex items-center gap-2">
                <x-admin::modern-dropdown value="Last week">
                    <button type="button" @click="selected = 'Last week'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selected === 'Last week' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">Last week</button>
                    <button type="button" @click="selected = 'This week'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selected === 'This week' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">This week</button>
                    <button type="button" @click="selected = 'This month'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selected === 'This month' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">This month</button>
                </x-admin::modern-dropdown>
                <button @click="editing = !editing"
                    class="flex size-9 items-center justify-center rounded-lg border border-content-border bg-white text-text-muted shadow-sm hover:bg-gray-50 cursor-pointer"
                    :class="editing ? 'ring-2 ring-primary/30 bg-primary/5 text-primary' : ''"
                    title="Edit layout">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                </button>
                <template x-if="editing">
                    <div class="flex items-center gap-2">
                        <button @click="openPanel()"
                            class="flex h-9 items-center gap-1.5 rounded-lg border border-content-border bg-white px-3 text-[12px] font-medium text-text-heading shadow-sm hover:bg-gray-50 transition-colors cursor-pointer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Add Widget
                        </button>
                        <button @click="saveLayout()"
                            class="flex h-9 items-center gap-1.5 rounded-lg border border-content-border bg-white px-3 text-[12px] font-medium text-text-heading shadow-sm hover:bg-gray-50 transition-colors cursor-pointer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Done
                        </button>
                    </div>
                </template>
            </div>
        </header>

        <div class="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-4">
            <div class="flex flex-col gap-4 lg:col-span-3">
                {{-- Grid zone (3 cards) --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ($gridWidgets as $widget)
                        <template x-if="gridShow[{{ $loop->index }}]">
                            <div class="bg-gray-100 rounded-2xl p-2"
                                 :style="`order: ${gridOrder.indexOf({{ $loop->index }})}`"
                                 :draggable="editing"
                                 @dragstart="if(editing) dragStart($event, {{ $loop->index }})"
                                 @dragover="allowDrop($event)"
                                 @drop="if(editing) drop($event, {{ $loop->index }})"
                                 @dragend="if(editing) dragEnd($event)">
                                <div class="flex items-center justify-between px-2 pb-2.5">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span x-show="editing" class="cursor-grab active:cursor-grabbing text-text-muted/40 hover:text-text-muted touch-none shrink-0">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><circle cx="9" cy="12" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="9" cy="5" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="19" r="1"/></svg>
                                        </span>
                                        <span class="text-[13px] font-medium text-text-muted truncate">{{ $widget->label() }}</span>
                                    </div>
                                    <button x-show="editing" @click="gridShow[{{ $loop->index }}] = false" class="text-text-muted/50 hover:text-red-500 transition-colors shrink-0 cursor-pointer" title="Remove widget">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-4">
                                    {{ $widget->render() }}
                                </div>
                            </div>
                        </template>
                        <template x-if="!gridShow[{{ $loop->index }}] && editing">
                            <div class="bg-gray-100 rounded-2xl p-2"
                                 :style="`order: ${gridOrder.indexOf({{ $loop->index }})}`"
                                 @click="clickedSlot = {{ $loop->index }}; openPanel()"
                                 @dragover="allowDrop($event)"
                                 @drop="if(editing) drop($event, {{ $loop->index }})">
                                <div class="flex items-center justify-center h-full rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/50 cursor-pointer hover:border-gray-400 hover:bg-gray-100/50 transition-colors">
                                    <span class="text-[13px] text-text-muted">+ Add Widget</span>
                                </div>
                            </div>
                        </template>
                    @endforeach
                </div>

                {{-- Chart zone --}}
                <div x-show="chartShow[0]" class="bg-gray-100 rounded-2xl p-2">
                    <div class="flex items-center justify-between px-2 pb-2.5">
                        <span class="text-[14px] font-medium text-text-heading" x-text="chartWidgets[0]?.label"></span>
                        <button x-show="editing" @click="chartShow[0] = false; toggleZoneWidget('chart', 0, true)" class="text-text-muted/50 hover:text-red-500 transition-colors shrink-0 cursor-pointer" title="Remove widget">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div id="chart-widget-container" class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-4">
                        {!! $chartWidgets->first() ? $chartWidgets->first()->render() : '' !!}
                    </div>
                </div>
                <div x-show="!chartShow[0] && editing" @click="clickedSlot = 'chart'; openPanel();" class="bg-gray-100 rounded-2xl p-2 cursor-pointer">
                    <div class="flex items-center justify-center h-32 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/50 hover:border-gray-400 hover:bg-gray-100/50 transition-colors">
                        <span class="text-[13px] text-text-muted">+ Add Chart Widget</span>
                    </div>
                </div>
            </div>

                {{-- List zone (right sidebar) --}}
            <div class="flex flex-col gap-4">
                <div x-show="listShow[0]" class="flex flex-col bg-gray-100 rounded-2xl p-2 flex-1 min-h-0">
                    <div class="flex items-center justify-between px-2 pb-2.5 shrink-0">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-text-muted shrink-0">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                            </svg>
                            <span class="text-[14px] font-medium text-text-heading" x-text="listWidgets[0]?.label"></span>
                        </div>
                        <button x-show="editing" @click="listShow[0] = false; toggleZoneWidget('list', 0, true)" class="text-text-muted/50 hover:text-red-500 transition-colors shrink-0 cursor-pointer" title="Remove widget">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div id="list-widget-container" class="flex flex-col flex-1 min-h-0">
                        {!! $listWidgets->first() ? $listWidgets->first()->render() : '' !!}
                    </div>
                </div>
                <div x-show="!listShow[0] && editing" @click="clickedSlot = 'list'; openPanel();" class="bg-gray-100 rounded-2xl p-2 cursor-pointer h-full">
                    <div class="flex items-center justify-center h-32 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/50 hover:border-gray-400 hover:bg-gray-100/50 transition-colors">
                        <span class="text-[13px] text-text-muted">+ Add List Widget</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table zone (full width) --}}
        <div class="mb-4">
            <div x-show="tableShow[0]" class="bg-gray-100 rounded-2xl p-2">
                <div class="flex items-center justify-between px-2 pb-2.5">
                    <span class="text-[14px] font-medium text-text-heading" x-text="tableWidgets[0]?.label"></span>
                    <button x-show="editing" @click="tableShow[0] = false; toggleZoneWidget('table', 0, true)" class="text-text-muted/50 hover:text-red-500 transition-colors shrink-0 cursor-pointer" title="Remove widget">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="table-widget-container">
                    {!! $tableWidgets->first() ? $tableWidgets->first()->render() : '' !!}
                </div>
            </div>
            <div x-show="!tableShow[0] && editing" @click="clickedSlot = 'table'; openPanel();" class="bg-gray-100 rounded-2xl p-2 cursor-pointer">
                <div class="flex items-center justify-center h-24 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/50 hover:border-gray-400 hover:bg-gray-100/50 transition-colors">
                    <span class="text-[13px] text-text-muted">+ Add Table Widget</span>
                </div>
            </div>
        </div>

    {{-- Side panel --}}
    <div
    x-show="panelOpen || panelClosing"
    class="fixed inset-0 z-[200] flex justify-end font-sans"
    style="display: none;"
>
    <div
        x-show="panelOpen || panelClosing"
        x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"
        @click="closePanel()"
    ></div>
    <div
        class="relative w-full max-w-[400px] bg-white shadow-2xl flex flex-col border-l border-gray-200 my-2 mr-2 rounded-xl h-[calc(100%-1rem)]"
        x-show="panelOpen || panelClosing"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
    >
        <div class="flex items-center justify-between px-5 py-3 shrink-0 border-b border-gray-200">
            <h2 class="text-lg font-bold text-text-heading">Widgets</h2>
            <button @click="closePanel()" class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-text-muted hover:text-text-primary transition-colors">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[18px]"><path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" /></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-3">
            {{-- Grid widgets --}}
            <template x-if="typeof clickedSlot === 'number' || clickedSlot === null">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-text-muted px-1 mb-2 mt-1">Widgets</h3>
                    <template x-for="w in gridWidgets" :key="w.index">
                        <button @click="addWidget(w.index)"
                            class="flex w-full items-center justify-between px-4 py-3 rounded-xl bg-content-bg border border-content-border/60 shadow-sm mb-2 text-left group hover:bg-white/50 transition-colors"
                            x-show="!gridShow[w.index]"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <template x-if="w.image">
                                    <img :src="w.image" alt="" class="size-7 shrink-0 rounded-lg object-cover">
                                </template>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-text-heading group-hover:text-primary transition-colors" x-text="w.label"></div>
                                </div>
                            </div>
                            <span class="inline-flex size-7 items-center justify-center rounded-full bg-content-border/40 text-text-muted transition-colors group-hover:bg-primary group-hover:text-white shrink-0 ml-3">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[14px]"><path d="M12 5v14M5 12h14" stroke-linecap="round" /></svg>
                            </span>
                        </button>
                    </template>
                    <template x-if="gridWidgets.filter(w => !gridShow[w.index]).length === 0">
                        <div class="text-center py-6 text-[13px] text-text-muted">All grid widgets are visible</div>
                    </template>
                </div>
            </template>

            {{-- Zone-specific sections --}}
            @foreach (['chart' => 'Chart', 'table' => 'Table', 'list' => 'List'] as $zone => $label)
            <template x-if="clickedSlot === '{{ $zone }}'">
                <div>
                    @if ($zone === 'table')
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-text-muted px-1 mb-2 mt-1">Forms</h3>
                    <template x-for="f in tableForms" :key="f.id">
                        <button @click="selectedFormId = f.id; selectZoneWidget('table', 'form_entries_table')"
                            class="flex w-full items-center justify-between px-4 py-3 rounded-xl bg-content-bg border border-content-border/60 shadow-sm mb-2 text-left group hover:bg-white/50 transition-colors"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                        <line x1="12" y1="18" x2="12" y2="12" />
                                        <line x1="9" y1="15" x2="15" y2="15" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-text-heading group-hover:text-primary transition-colors" x-text="f.title"></div>
                                </div>
                            </div>
                            <span class="inline-flex size-7 items-center justify-center rounded-full bg-content-border/40 text-text-muted transition-colors group-hover:bg-primary group-hover:text-white shrink-0 ml-3">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[14px]"><path d="M12 5v14M5 12h14" stroke-linecap="round" /></svg>
                            </span>
                        </button>
                    </template>
                    <template x-if="tableForms.length === 0">
                        <div class="text-center py-6 text-[13px] text-text-muted">No forms yet. <a href="{{ route('admin.forms.create') }}" class="text-primary font-medium no-underline">Create one</a>.</div>
                    </template>
                    @else
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-text-muted px-1 mb-2 mt-1">{{ $label }} Widgets</h3>
                    <template x-for="w in (allByZone['{{ $zone }}'] || [])" :key="w.type">
                        <button @click="selectZoneWidget('{{ $zone }}', w.type)"
                            class="flex w-full items-center justify-between px-4 py-3 rounded-xl bg-content-bg border border-content-border/60 shadow-sm mb-2 text-left group hover:bg-white/50 transition-colors"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <template x-if="w.image">
                                    <img :src="w.image" alt="" class="size-7 shrink-0 rounded-lg object-cover">
                                </template>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-text-heading group-hover:text-primary transition-colors" x-text="w.label"></div>
                                </div>
                            </div>
                            <span class="inline-flex size-7 items-center justify-center rounded-full bg-content-border/40 text-text-muted transition-colors group-hover:bg-primary group-hover:text-white shrink-0 ml-3">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[14px]"><path d="M12 5v14M5 12h14" stroke-linecap="round" /></svg>
                            </span>
                        </button>
                    </template>
                    <template x-if="(allByZone['{{ $zone }}'] || []).length === 0">
                        <div class="text-center py-6 text-[13px] text-text-muted">No widgets available for this section</div>
                    </template>
                    @endif
                </div>
            </template>
            @endforeach
        </div>
    </div>
</div>
</div>
@endsection
