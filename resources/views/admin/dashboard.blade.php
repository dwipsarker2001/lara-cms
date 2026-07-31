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
        allCollections: @json($allCollections->values()),
    };
    function dashboard() {
        return {
            period: 'Today',
            selected: 'Today',
            editing: false,
            searchQuery: '',
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
            allCollections: window._dashboardData.allCollections,
            selectedFormId: null,
            dragIdx: null,
            clickedSlot: null,
            activeTab: 'grid',
            panelOpen: false,
            panelClosing: false,
            shouldShow(itemPeriod, title, sub) {
                let matchesPeriod = false;
                if (this.period === 'Today') {
                    matchesPeriod = itemPeriod === 'Today';
                } else if (this.period === 'Yesterday') {
                    matchesPeriod = itemPeriod === 'Yesterday';
                } else if (this.period === 'This week') {
                    matchesPeriod = ['Today', 'Yesterday', 'This week'].includes(itemPeriod);
                }

                if (!matchesPeriod) return false;

                if (!this.searchQuery) return true;
                const query = this.searchQuery.toLowerCase();
                return title.toLowerCase().includes(query) || sub.toLowerCase().includes(query);
            },
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
            openPanel(slot = null) {
                if (slot !== null) this.clickedSlot = slot;
                if (this.clickedSlot === 'chart') this.activeTab = 'chart';
                else if (this.clickedSlot === 'table') this.activeTab = 'table';
                else if (this.clickedSlot === 'list') this.activeTab = 'list';
                else this.activeTab = 'grid';
                this.panelOpen = true;
            },
            closePanel() {
                this.panelClosing = true;
                setTimeout(() => {
                    this.panelClosing = false;
                    this.panelOpen = false;
                }, 200);
            },
            addGridWidget(type, formId = null) {
                let currentOrder = this.gridOrder
                    .filter(i => this.gridShow[i])
                    .map(i => {
                        let w = this.gridWidgets[i];
                        if (!w) return null;
                        return w.form_id ? { type: w.type, form_id: w.form_id } : w.type;
                    })
                    .filter(Boolean);

                let newItem = formId ? { type: type, form_id: formId } : type;

                if (typeof this.clickedSlot === 'number' && this.clickedSlot < currentOrder.length) {
                    currentOrder.splice(this.clickedSlot, 0, newItem);
                } else {
                    currentOrder.push(newItem);
                }

                let payload = {
                    grid: {
                        order: currentOrder,
                        hidden: []
                    }
                };

                fetch('/admin/widgets/layout', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]')?.content || ''},
                    body: JSON.stringify(payload),
                }).then(() => location.reload());
            },
            removeGridWidget(idx) {
                let currentOrder = this.gridOrder
                    .filter(i => i !== idx && this.gridShow[i])
                    .map(i => {
                        let w = this.gridWidgets[i];
                        if (!w) return null;
                        return w.form_id ? { type: w.type, form_id: w.form_id } : w.type;
                    })
                    .filter(Boolean);

                let payload = {
                    grid: {
                        order: currentOrder,
                        hidden: []
                    }
                };

                fetch('/admin/widgets/layout', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]')?.content || ''},
                    body: JSON.stringify(payload),
                }).then(() => location.reload());
            },
            selectZoneWidget(zone, type) {
                if (zone === 'grid') {
                    this.addGridWidget(type);
                    return;
                }
                let savePayload = {order: [type], hidden: []};
                let renderPayload = {zone, type};
                if (zone === 'table' && this.selectedFormId) {
                    savePayload.form_id = this.selectedFormId;
                    renderPayload.form_id = this.selectedFormId;
                }
                let payload = {};
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
                let currentGridOrder = this.gridOrder
                    .filter(i => this.gridShow[i])
                    .map(i => {
                        let w = this.gridWidgets[i];
                        if (!w) return null;
                        return w.form_id ? { type: w.type, form_id: w.form_id } : w.type;
                    })
                    .filter(Boolean);

                let payload = {
                    grid: { order: currentGridOrder, hidden: [] },
                    chart: { order: [this.chartWidgets[0]?.type].filter(Boolean), hidden: !this.chartShow[0] ? [0] : [] },
                    table: { order: [this.tableWidgets[0]?.type].filter(Boolean), hidden: !this.tableShow[0] ? [0] : [] },
                    list: { order: [this.listWidgets[0]?.type].filter(Boolean), hidden: !this.listShow[0] ? [0] : [] },
                };
                fetch('/admin/widgets/layout', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('[name=csrf-token]')?.content || ''},
                    body: JSON.stringify(payload),
                }).then(() => {
                    this.editing = false;
                });
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
                <button @click="editing = true" x-show="!editing"
                    class="flex h-9 items-center gap-1.5 rounded-lg border border-content-border bg-white px-3 text-[12px] font-medium text-text-heading shadow-sm hover:bg-gray-50 transition-colors cursor-pointer"
                    title="Edit layout">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4 text-text-muted"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                    Edit Layout
                </button>
                <template x-if="editing">
                    <div class="flex items-center gap-2">
                        <button @click="openPanel('grid')"
                            class="flex h-9 items-center gap-1.5 rounded-lg bg-primary px-3 text-[12px] font-medium text-white shadow-sm hover:bg-primary/90 transition-colors cursor-pointer">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><path d="M12 5v14M5 12h14" stroke-linecap="round" /></svg>
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
                {{-- Grid zone (cards) --}}
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
                                        @if ($widget->type() === 'visitor')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3.5 text-text-muted shrink-0"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        @elseif ($widget->type() === 'form_stat')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3.5 text-text-muted shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                                        @elseif ($widget->type() === 'collection_count')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3.5 text-text-muted shrink-0"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                                        @endif
                                        <span class="text-[13px] font-medium text-text-muted truncate" id="widget-label-{{ $loop->index }}">{{ $widget->label() }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        <template x-if="!editing">
                                            <div>
                                                @if ($widget->type() === 'form_stat')
                                                    <div x-data="{
                                                        showSettings: false,
                                                        selectedFormId: {{ property_exists($widget, 'formId') && $widget->formId ? (int) $widget->formId : 'null' }},
                                                        async changeForm(formId) {
                                                            this.selectedFormId = formId;
                                                            this.showSettings = false;
                                                            const token = document.querySelector('[name=csrf-token]')?.content || '';
                                                            try {
                                                                const response = await fetch(@js(route('admin.widgets.render')), {
                                                                    method: 'POST',
                                                                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                                                                    body: JSON.stringify({
                                                                        zone: 'grid',
                                                                        type: 'form_stat',
                                                                        form_id: formId,
                                                                    }),
                                                                });
                                                                const data = await response.json();
                                                                const container = document.getElementById('widget-content-{{ $loop->index }}');
                                                                if (container && data.html) {
                                                                    container.innerHTML = data.html;
                                                                    if (window.Alpine) Alpine.initTree(container);
                                                                    const labelSpan = document.getElementById('widget-label-{{ $loop->index }}');
                                                                    if (labelSpan && data.label) {
                                                                        labelSpan.textContent = data.label;
                                                                    }
                                                                }
                                                            } catch(e) {}
                                                        }
                                                    }" class="relative">
                                                        <button @click="showSettings = !showSettings" class="text-text-muted hover:text-text-primary p-0.5 rounded transition-colors cursor-pointer" title="Select Form">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 1 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                                                        </button>
                                                        <div x-show="showSettings" @click.outside="showSettings = false" class="absolute right-0 top-full mt-1.5 min-w-[10rem] bg-white border border-gray-200 rounded-xl shadow-xl p-1.5 space-y-0.5 z-[100]" style="display: none;">
                                                            <button @click="changeForm(null)" class="flex w-full items-center justify-between px-2.5 py-1.5 rounded-lg text-xs hover:bg-gray-50 transition-colors text-left cursor-pointer" :class="!selectedFormId ? 'bg-primary/5 text-primary font-medium' : 'text-text-primary'">
                                                                <span>All Forms</span>
                                                                <span x-show="!selectedFormId" class="font-bold">✓</span>
                                                            </button>
                                                            <template x-for="f in tableForms" :key="f.id">
                                                                <button @click="changeForm(f.id)" class="flex w-full items-center justify-between px-2.5 py-1.5 rounded-lg text-xs hover:bg-gray-50 transition-colors text-left cursor-pointer" :class="selectedFormId == f.id ? 'bg-primary/5 text-primary font-medium' : 'text-text-primary'">
                                                                    <span x-text="f.title"></span>
                                                                    <span x-show="selectedFormId == f.id" class="font-bold">✓</span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                @elseif ($widget->type() === 'collection_count')
                                                    <div x-data="{
                                                        showSettings: false,
                                                        selectedSlug: localStorage.getItem('widget_col_slug') || (allCollections[0]?.slug || ''),
                                                        selectCollection(slug) {
                                                            this.selectedSlug = slug;
                                                            localStorage.setItem('widget_col_slug', slug);
                                                            this.showSettings = false;
                                                            location.reload();
                                                        }
                                                    }" class="relative">
                                                        <button @click="showSettings = !showSettings" class="text-text-muted hover:text-text-primary p-0.5 rounded transition-colors cursor-pointer" title="Select Collection">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 1 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                                                        </button>
                                                        <div x-show="showSettings" @click.outside="showSettings = false" class="absolute right-0 top-full mt-1.5 min-w-[10rem] bg-white border border-gray-200 rounded-xl shadow-xl p-1.5 space-y-0.5 z-[100]" style="display: none;">
                                                            <template x-for="c in allCollections" :key="c.slug">
                                                                <button @click="selectCollection(c.slug)" class="flex w-full items-center justify-between px-2.5 py-1.5 rounded-lg text-xs hover:bg-gray-50 transition-colors text-left cursor-pointer" :class="selectedSlug === c.slug ? 'bg-primary/5 text-primary font-medium' : 'text-text-primary'">
                                                                    <span x-text="c.name"></span>
                                                                    <span x-show="selectedSlug === c.slug" class="font-bold">✓</span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </template>

                                        <button x-show="editing" @click="removeGridWidget({{ $loop->index }})" class="text-text-muted/50 hover:text-red-500 transition-colors shrink-0 cursor-pointer" title="Remove widget">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <div id="widget-content-{{ $loop->index }}" class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-4">
                                    {{ $widget->render() }}
                                </div>
                            </div>
                        </template>
                    @endforeach

                    {{-- Add Widget card (only visible in edit mode) --}}
                    <div x-show="editing" class="bg-gray-100 rounded-2xl p-2 min-h-[110px]"
                         :style="`order: 9999`"
                         @click="openPanel('grid')">
                        <div class="flex flex-col items-center justify-center h-full min-h-[100px] rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/60 cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-colors p-4 group">
                            <span class="inline-flex size-8 items-center justify-center rounded-full bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors mb-1">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path d="M12 5v14M5 12h14" stroke-linecap="round" /></svg>
                            </span>
                            <span class="text-[12px] font-semibold text-text-heading group-hover:text-primary transition-colors">+ Add Widget</span>
                        </div>
                    </div>
                </div>

                {{-- Chart zone --}}
                <div x-show="chartShow[0]" class="bg-gray-100 rounded-2xl p-2">
                    <div class="flex items-center justify-between px-2 pb-2.5 flex-wrap gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-text-muted shrink-0"><path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 5-5"/></svg>
                            <span class="text-[14px] font-medium text-text-heading" x-text="chartWidgets[0]?.label">Website Analytics</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <template x-if="!editing && chartWidgets[0]?.type === 'website_analytics'">
                                <div x-data="{ selectedPeriod: '7 Days' }" class="flex items-center gap-1 rounded-lg bg-gray-200/60 p-1">
                                    <template x-for="opt in ['Today', '7 Days', '30 Days', 'This Year']">
                                        <button
                                            type="button"
                                            @click="selectedPeriod = opt; window.dispatchEvent(new CustomEvent('analytics-period-change', { detail: opt }))"
                                            class="rounded-md px-2.5 py-1 text-[11px] transition-all cursor-pointer"
                                            :class="selectedPeriod === opt ? 'bg-white text-text-heading font-semibold shadow-sm' : 'text-text-muted hover:text-text-heading font-medium hover:bg-white/50'"
                                            x-text="opt"
                                        ></button>
                                    </template>
                                </div>
                            </template>
                            <button x-show="editing" @click="chartShow[0] = false; toggleZoneWidget('chart', 0, true)" class="text-text-muted/50 hover:text-red-500 transition-colors shrink-0 cursor-pointer" title="Remove widget">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    <div id="chart-widget-container" class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-4">
                        {!! $chartWidgets->first() ? $chartWidgets->first()->render() : '' !!}
                    </div>
                </div>
                <div x-show="!chartShow[0] && editing" @click="openPanel('chart')" class="bg-gray-100 rounded-2xl p-2 cursor-pointer">
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
                <div x-show="!listShow[0] && editing" @click="openPanel('list')" class="bg-gray-100 rounded-2xl p-2 cursor-pointer h-full">
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
                    <div class="flex items-center gap-2 min-w-0">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-text-muted shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                        <span class="text-[14px] font-medium text-text-heading" x-text="tableWidgets[0]?.label"></span>
                    </div>
                    <button x-show="editing" @click="tableShow[0] = false; toggleZoneWidget('table', 0, true)" class="text-text-muted/50 hover:text-red-500 transition-colors shrink-0 cursor-pointer" title="Remove widget">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="table-widget-container">
                    {!! $tableWidgets->first() ? $tableWidgets->first()->render() : '' !!}
                </div>
            </div>
            <div x-show="!tableShow[0] && editing" @click="openPanel('table')" class="bg-gray-100 rounded-2xl p-2 cursor-pointer">
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
            class="relative w-full max-w-[420px] bg-white shadow-2xl flex flex-col border-l border-gray-200 my-2 mr-2 rounded-xl h-[calc(100%-1rem)]"
            x-show="panelOpen || panelClosing"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
        >
            <div class="flex items-center justify-between px-5 py-3 shrink-0 border-b border-gray-200">
                <h2 class="text-lg font-bold text-text-heading">Add Widgets</h2>
                <button @click="closePanel()" class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-text-muted hover:text-text-primary transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[18px]"><path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" /></svg>
                </button>
            </div>

            {{-- Tabbed Navigation inside Sidepanel --}}
            <div class="flex border-b border-gray-200 bg-gray-50/60 px-2 text-xs font-medium shrink-0">
                <button @click="activeTab = 'grid'"
                    class="py-2.5 px-3 border-b-2 font-semibold transition-colors cursor-pointer"
                    :class="activeTab === 'grid' ? 'border-primary text-primary' : 'border-transparent text-text-muted hover:text-text-heading'"
                >Grid Cards</button>
                <button @click="activeTab = 'chart'"
                    class="py-2.5 px-3 border-b-2 font-semibold transition-colors cursor-pointer"
                    :class="activeTab === 'chart' ? 'border-primary text-primary' : 'border-transparent text-text-muted hover:text-text-heading'"
                >Chart</button>
                <button @click="activeTab = 'table'"
                    class="py-2.5 px-3 border-b-2 font-semibold transition-colors cursor-pointer"
                    :class="activeTab === 'table' ? 'border-primary text-primary' : 'border-transparent text-text-muted hover:text-text-heading'"
                >Form Table</button>
                <button @click="activeTab = 'list'"
                    class="py-2.5 px-3 border-b-2 font-semibold transition-colors cursor-pointer"
                    :class="activeTab === 'list' ? 'border-primary text-primary' : 'border-transparent text-text-muted hover:text-text-heading'"
                >List</button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                {{-- Grid Cards Tab --}}
                <div x-show="activeTab === 'grid'">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-text-muted px-1 mb-3">Available Grid Widgets</h3>
                    <template x-for="w in (allByZone['grid'] || [])" :key="w.type">
                        <div class="mb-3 rounded-xl bg-content-bg border border-content-border/60 p-3 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <template x-if="w.image">
                                        <img :src="w.image" alt="" class="size-7 shrink-0 rounded-lg object-cover">
                                    </template>
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold text-text-heading" x-text="w.label"></div>
                                    </div>
                                </div>
                                <template x-if="w.type !== 'form_stat'">
                                    <button @click="addGridWidget(w.type)"
                                        class="inline-flex h-8 items-center gap-1 rounded-lg bg-primary px-3 text-xs font-medium text-white hover:bg-primary/90 transition-colors shadow-sm cursor-pointer"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><path d="M12 5v14M5 12h14" stroke-linecap="round" /></svg>
                                        Add
                                    </button>
                                </template>
                            </div>

                            <template x-if="w.type === 'form_stat'">
                                <div class="mt-2.5 space-y-1.5 border-t border-content-border/40 pt-2">
                                    <div class="flex items-center justify-between text-xs py-1">
                                        <span class="font-medium text-text-heading">All Forms Overview</span>
                                        <button @click="addGridWidget('form_stat', null)"
                                            class="inline-flex h-7 items-center gap-1 rounded-md bg-primary px-2.5 text-[11px] font-medium text-white hover:bg-primary/90 transition-colors shadow-sm cursor-pointer"
                                        >
                                            + Add
                                        </button>
                                    </div>
                                    <template x-for="f in tableForms" :key="f.id">
                                        <div class="flex items-center justify-between text-xs py-1">
                                            <span class="text-text-muted truncate max-w-[200px]" x-text="f.title"></span>
                                            <button @click="addGridWidget('form_stat', f.id)"
                                                class="inline-flex h-7 items-center gap-1 rounded-md border border-content-border bg-white px-2.5 text-[11px] font-medium text-text-heading hover:bg-gray-50 transition-colors shadow-sm cursor-pointer"
                                            >
                                                + Add
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Chart Tab --}}
                <div x-show="activeTab === 'chart'">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-text-muted px-1 mb-3">Chart Widgets</h3>
                    <template x-for="w in (allByZone['chart'] || [])" :key="w.type">
                        <button @click="selectZoneWidget('chart', w.type)"
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
                </div>

                {{-- Table Tab --}}
                <div x-show="activeTab === 'table'">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-text-muted px-1 mb-3">Form Submissions Table</h3>
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
                </div>

                {{-- List Tab --}}
                <div x-show="activeTab === 'list'">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-text-muted px-1 mb-3">List Widgets</h3>
                    <template x-for="w in (allByZone['list'] || [])" :key="w.type">
                        <button @click="selectZoneWidget('list', w.type)"
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
