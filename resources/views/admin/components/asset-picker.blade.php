@props([
    'pickerOpen' => false,
    'pickerCallback' => null,
])

<div
    x-data="assetPicker()"
    x-init="init({{ $pickerOpen ? 'true' : 'false' }}, @js($pickerCallback))"
    x-on:open-asset-picker.window="open($event.detail.callback)"
    x-show="isOpen || closing"
    class="fixed inset-0 z-[100] flex justify-end font-sans"
    style="display: none;"
>
    <div
        x-show="isOpen || closing"
        x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"
        @click="close()"
    ></div>

    <div
        class="relative w-full max-w-[400px] bg-white shadow-2xl flex flex-col border-l border-gray-200 my-2 mr-2 rounded-xl h-[calc(100%-1rem)]"
        x-show="isOpen || closing"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3 shrink-0 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Assets</h2>
            <div class="flex items-center gap-2">
                <label class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors cursor-pointer shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                        <polyline points="17 8 12 3 7 8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                    </svg>
                    <input type="file" accept="image/*,.pdf,.doc,.docx,.zip" class="hidden" @change="upload($event)">
                </label>
                <button @click="createDir()" class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                        <line x1="12" y1="11" x2="12" y2="15" />
                        <line x1="10" y1="13" x2="14" y2="13" />
                    </svg>
                </button>
                <button @click="close()" class="size-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-400 transition-colors shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="size-5.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Delete Banner --}}
        <div x-show="deleteConfirm" x-cloak class="shrink-0 px-7 py-3 bg-gradient-to-r from-red-50 to-red-50/80 flex items-center gap-1">
            <p class="flex-1 text-sm text-red-700">Do you really want to delete?</p>
            <button @click="deleteConfirm = null" class="size-8 flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-100 transition-all shrink-0" title="Cancel">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <button @click="confirmDelete()" class="size-8 flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-100 transition-all shrink-0" title="Confirm">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            </button>
        </div>

        {{-- Drop Zone --}}
        <div
            class="flex-1 overflow-y-auto transition-colors relative"
            :class="dragOver ? 'bg-primary/5' : ''"
            style="scrollbar-width: none;"
            @dragenter.prevent="onDragEnter"
            @dragleave.prevent="onDragLeave"
            @dragover.prevent="onDragOver"
            @drop.prevent="onDrop"
        >
            <div class="flex flex-col min-h-full px-1 pb-1">

                {{-- Breadcrumbs --}}
                <template x-if="currentDirectory">
                    <nav class="sticky top-0 z-10 bg-white flex items-center gap-1 py-4 mb-3 overflow-x-auto whitespace-nowrap border-b border-gray-50" style="scrollbar-width: none;">
                        <template x-for="(crumb, i) in breadcrumbs" :key="crumb.path">
                            <div class="flex items-center gap-1 shrink-0">
                                <template x-if="i > 0">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-gray-300 shrink-0"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                                </template>
                                <button
                                    @click="setDirectory(crumb.path)"
                                    @dragenter.prevent="crumbDragEnter(crumb.path)"
                                    @dragleave.prevent="crumbDragLeave()"
                                    @dragover.prevent
                                    @drop.prevent="crumbDrop($event, crumb.path)"
                                    class="px-2 py-1 rounded-md text-xs transition-colors whitespace-nowrap"
                                    :class="crumbDragActive === crumb.path ? 'bg-primary/15 ring-2 ring-primary/60 outline-2 outline-dashed outline-primary/40 outline-offset-[-2px]' : (i === breadcrumbs.length - 1 ? 'text-gray-900 font-bold' : 'text-gray-400 hover:text-primary hover:bg-primary/5')"
                                    x-text="crumb.name"
                                ></button>
                            </div>
                        </template>
                    </nav>
                </template>

                {{-- Loading --}}
                <template x-if="loading">
                    <div class="flex items-center justify-center py-20">
                        <div class="animate-spin size-9 border-[3px] border-primary/10 border-t-primary rounded-full"></div>
                    </div>
                </template>

                {{-- Grid --}}
                <template x-if="!loading && allItems.length > 0">
                    <div class="grid grid-cols-3 gap-x-4 gap-y-6 p-5">
                        <template x-for="(item, idx) in allItems" :key="item.id">
                            <div
                                draggable="true"
                                class="group relative flex flex-col rounded-lg border bg-white shadow-sm transition-all duration-150 cursor-pointer"
                                :class="dragTargetId === item.id ? 'border-primary border-2' : 'border-gray-100 hover:border-primary/30'"
                                @dragstart="onCardDragStart($event, item)"
                                @dragend="onCardDragEnd()"
                                @dragenter.prevent="onCardDragEnter(item)"
                                @dragleave.prevent="onCardDragLeave(item)"
                                @dragover.prevent
                                @drop.prevent="onCardDrop($event, item)"
                            >
                                {{-- Thumbnail --}}
                                <div class="relative aspect-[1/1] bg-[#F3F4F6] overflow-hidden rounded-t-lg">
                                    <button @click="selectItem(item)" class="size-full flex items-center justify-center">
                                        <template x-if="item.is_directory">
                                            <svg viewBox="0 0 48 48" class="size-16" fill="none">
                                                <path d="M4 10C4 7.79086 5.79086 6 8 6H18.7242C19.9045 6 21.011 6.52552 21.7505 7.43906L25.3218 11.8594C25.6915 12.3162 26.2448 12.5789 26.8323 12.5789H40C42.2091 12.5789 44 14.3681 44 16.5772V38C44 40.2091 42.2091 42 40 42H8C5.79086 42 4 40.2091 4 38V10Z" fill="#F59E0B" />
                                                <path opacity="0.25" d="M4 16.5771C4 14.368 5.79086 12.5789 8 12.5789H40C42.2091 12.5789 44 14.3681 44 16.5772V38C44 40.2091 42.2091 42 40 42H8C5.79086 42 4 40.2091 4 38V16.5771Z" fill="white" />
                                            </svg>
                                        </template>
                                        <template x-if="!item.is_directory">
                                            <img :src="`/storage/${item.path}`" :alt="item.name" class="size-full object-cover" x-on:error="if ($el.tagName === 'IMG') { $el.style.display='none'; $el.parentElement.innerHTML='<svg class=\"size-8 text-gray-300\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path d=\"M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z\" /><polyline points=\"14 2 14 8 20 8\" /></svg>'; }">
                                        </template>
                                    </button>
                                </div>

                                {{-- Action Menu --}}
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-[60]">
                                    <div class="relative" x-data="{ menuOpen: false }" @click.outside="menuOpen = false">
                                        <button @click="menuOpen = !menuOpen" class="size-7 flex items-center justify-center rounded-md bg-white/90 border border-gray-200 hover:bg-white text-gray-500 hover:text-gray-700 cursor-pointer">
                                            <svg viewBox="0 0 16 3" class="size-4" fill="currentColor"><circle cx="2" cy="1.5" r="1.5" /><circle cx="8" cy="1.5" r="1.5" /><circle cx="14" cy="1.5" r="1.5" /></svg>
                                        </button>
                                        <div x-show="menuOpen" x-cloak @click="menuOpen = false"
                                            class="absolute right-0 top-full mt-1 z-[70] min-w-[10rem] rounded-xl border border-gray-200 bg-white shadow-xl p-1.5"
                                        >
                                            <button type="button" role="menuitem" @click="menuOpen = false; startRename(item)"
                                                class="flex w-full items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 cursor-pointer"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-gray-400"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" /><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" /></svg>
                                                <span>Rename</span>
                                            </button>
                                            <hr class="my-1 border-gray-100">
                                            <button type="button" role="menuitem" @click="menuOpen = false; deleteConfirm = item"
                                                class="flex w-full items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-red-600 hover:bg-red-50 cursor-pointer"
                                            >
                                                <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-red-500"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c-.84 0-1.673.025-2.5.075V3.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.325C11.673 4.025 10.84 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
                                                <span>Delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Name / Rename Input --}}
                                <template x-if="renamingId === item.id">
                                    <div class="px-2 py-1.5">
                                        <input
                                            type="text"
                                            x-ref="renameInput"
                                            x-model="renameValue"
                                            @keydown.enter="doRename(item.id)"
                                            @keydown.escape="renamingId = null"
                                            @blur="doRename(item.id)"
                                            class="w-full h-full text-[13px] font-bold text-gray-900 bg-transparent outline-none"
                                        >
                                    </div>
                                </template>
                                <template x-if="renamingId !== item.id">
                                    <div class="border-t border-gray-100 px-3 py-2.5">
                                        <p class="text-[13px] font-bold text-gray-900 truncate leading-tight" @dblclick="startRename(item)" x-text="item.name"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Empty --}}
                <template x-if="!loading && allItems.length === 0">
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <h4 class="text-xl font-bold text-slate-500">There are no items here!</h4>
                        <p class="text-sm text-slate-400 mt-1 font-medium">Start adding your documents</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function assetPicker() {
        return {
            isOpen: false,
            closing: false,
            callback: null,
            assets: [],
            loading: false,
            currentDirectory: '',
            renamingId: null,
            renameValue: '',
            deleteConfirm: null,
            dragOver: false,
            dragCounter: 0,
            dragTargetId: null,
            crumbDragActive: null,

            init(open, cb) {
                if (open) { this.isOpen = true; this.callback = cb; this.fetchAssets(); }
            },

            open(callback) {
                this.callback = callback;
                this.isOpen = true;
                this.currentDirectory = '';
                this.assets = [];
                this.renamingId = null;
                this.deleteConfirm = null;
                this.$nextTick(() => this.fetchAssets());
            },

            close() {
                this.closing = true;
                setTimeout(() => {
                    this.closing = false;
                    this.isOpen = false;
                    this.callback = null;
                }, 200);
            },

            get breadcrumbs() {
                const crumbs = [{ name: 'Assets', path: '' }];
                if (!this.currentDirectory) return crumbs;
                const clean = this.currentDirectory.replace(/^assets\/?/, '');
                if (!clean) return crumbs;
                const parts = clean.split('/').filter(Boolean);
                let acc = '';
                for (const part of parts) {
                    acc = acc ? acc + '/' + part : part;
                    crumbs.push({ name: part, path: 'assets/' + acc });
                }
                return crumbs;
            },

            get allItems() {
                return [
                    ...this.assets.filter(a => a.is_directory).sort((a, b) => a.name.localeCompare(b.name)),
                    ...this.assets.filter(a => !a.is_directory).sort((a, b) => a.name.localeCompare(b.name)),
                ];
            },

            async fetchAssets() {
                this.loading = true;
                const params = new URLSearchParams({ directory: this.currentDirectory });
                try {
                    const res = await fetch(`{{ route("admin.assets.list") }}?${params}`);
                    const data = await res.json();
                    this.assets = data.assets || [];
                } catch {} finally {
                    this.loading = false;
                }
            },

            setDirectory(dir) {
                this.currentDirectory = dir;
                this.fetchAssets();
            },

            selectItem(item) {
                if (item.is_directory) {
                    this.setDirectory(item.path);
                } else if (this.callback) {
                    this.callback(`/storage/${item.path}`);
                    this.close();
                }
            },

            async upload(event) {
                const file = event.target.files?.[0];
                if (!file) return;
                const formData = new FormData();
                formData.append('file', file);
                if (this.currentDirectory) formData.append('directory', this.currentDirectory);
                try {
                    await fetch('{{ route("admin.assets.store") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData,
                    });
                } catch {}
                event.target.value = '';
                this.fetchAssets();
            },

            async createDir() {
                const existingNames = new Set(this.assets.filter(a => a.is_directory).map(a => a.name));
                let name = 'New Folder';
                while (existingNames.has(name)) {
                    name = 'New Folder (' + (parseInt((name.match(/\((\d+)\)/) || [0, 0])[1]) + 1) + ')';
                }
                try {
                    const res = await fetch('{{ route("admin.assets.directory") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ name, directory: this.currentDirectory || undefined }),
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    const newId = data?.id;
                    await this.fetchAssets();
                    if (newId) {
                        this.renamingId = newId;
                        this.renameValue = name;
                        this.$nextTick(() => {
                            if (this.$refs.renameInput) setTimeout(() => this.$refs.renameInput.select(), 50);
                        });
                    }
                } catch {}
            },

            startRename(item) {
                this.renamingId = item.id;
                this.renameValue = item.name;
                this.$nextTick(() => {
                    if (this.$refs.renameInput) setTimeout(() => this.$refs.renameInput.select(), 50);
                });
            },

            async doRename(id) {
                if (!this.renameValue.trim()) { this.renamingId = null; return; }
                try {
                    await fetch(`/admin/assets/${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ name: this.renameValue.trim() }),
                    });
                    this.renamingId = null;
                    this.fetchAssets();
                } catch {}
            },

            async confirmDelete() {
                if (!this.deleteConfirm) return;
                try {
                    await fetch(`/admin/assets/${this.deleteConfirm.id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    });
                    this.deleteConfirm = null;
                    this.fetchAssets();
                } catch {}
            },

            onDragEnter(e) {
                this.dragCounter++;
                if (e.dataTransfer.items && e.dataTransfer.items.length > 0) {
                    this.dragOver = true;
                }
            },

            onDragLeave(e) {
                this.dragCounter--;
                if (this.dragCounter <= 0) { this.dragCounter = 0; this.dragOver = false; }
            },

            onDragOver(e) {},

            onDrop(e) {
                this.dragOver = false;
                this.dragCounter = 0;
                const files = Array.from(e.dataTransfer.files);
                const uploadPromises = files.map(file => {
                    const formData = new FormData();
                    formData.append('file', file);
                    if (this.currentDirectory) formData.append('directory', this.currentDirectory);
                    return fetch('{{ route("admin.assets.store") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData,
                    });
                });
                Promise.all(uploadPromises).then(() => this.fetchAssets());
            },

            onCardDragStart(e, item) {
                e.dataTransfer.setData('text/plain', JSON.stringify({ assetId: item.id }));
                e.dataTransfer.effectAllowed = 'move';
            },

            onCardDragEnd() {
                this.dragTargetId = null;
            },

            onCardDragEnter(item) {
                if (item.is_directory) this.dragTargetId = item.id;
            },

            onCardDragLeave(item) {
                if (this.dragTargetId === item.id) this.dragTargetId = null;
            },

            async onCardDrop(e, item) {
                if (!item.is_directory) return;
                this.dragTargetId = null;
                const raw = e.dataTransfer.getData('text/plain');
                try {
                    const d = JSON.parse(raw);
                    if (d.assetId) {
                        await fetch(`/admin/assets/${d.assetId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ directory: item.path }),
                        });
                        this.fetchAssets();
                        return;
                    }
                } catch {}
                const files = Array.from(e.dataTransfer.files);
                const uploadPromises = files.map(file => {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('directory', item.path);
                    return fetch('{{ route("admin.assets.store") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData,
                    });
                });
                Promise.all(uploadPromises).then(() => this.fetchAssets());
            },

            crumbDragEnter(path) { this.crumbDragActive = path; },
            crumbDragLeave() { this.crumbDragActive = null; },
            async crumbDrop(e, path) {
                this.crumbDragActive = null;
                const raw = e.dataTransfer.getData('text/plain');
                try {
                    const d = JSON.parse(raw);
                    if (d.assetId) {
                        await fetch(`/admin/assets/${d.assetId}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ directory: path }),
                        });
                        this.fetchAssets();
                    }
                } catch {}
            },
        };
    }
</script>
@endpush
