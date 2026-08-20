{{-- link (Collection / Custom URL picker) --}}
<template x-if="field.type === 'link'">
    <div :data-field-target="field.name">
        <label class="block text-sm font-semibold text-text-primary mb-1" x-text="field.label"></label>
        <div class="flex gap-2">
            {{-- Mode / Collection selector --}}
            <div class="relative w-40 shrink-0" @keydown="selectKeydown($event, 'mode-' + field.name)">
                <button type="button" @click.stop="toggleSelect('mode-' + field.name)"
                    class="w-full flex items-center justify-between gap-2 bg-white border border-gray-300 text-sm rounded-lg px-3 h-10 cursor-pointer transition-shadow duration-150 hover:shadow-sm"
                    :class="getSelect('mode-' + field.name).open ? 'ring-2 ring-primary/30 border-primary shadow-sm' : 'shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)]'">
                    <span class="text-text-primary truncate font-medium" x-text="getLinkModeLabel(getLinkMode(field.name))"></span>
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 opacity-60 transition-transform duration-150" :class="getSelect('mode-' + field.name).open ? 'rotate-180' : ''">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-show="getSelect('mode-' + field.name).open" x-cloak
                    class="absolute z-50 top-full mt-1.5 left-0 right-0 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
                    @click.away="closeSelect('mode-' + field.name)">
                    <div class="py-1 max-h-56 overflow-y-auto [scrollbar-width:thin]">
                        {{-- Custom Option --}}
                        <button type="button" @click="setLinkMode(field.name, 'custom'); closeSelect('mode-' + field.name)"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm text-left transition-colors duration-75 hover:bg-primary/10"
                            :class="getLinkMode(field.name) === 'custom' ? 'text-primary font-semibold bg-primary/5' : 'text-gray-700'">
                            <span class="truncate">Custom</span>
                            <svg x-show="getLinkMode(field.name) === 'custom'" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-primary">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div class="h-px bg-gray-100 my-1"></div>

                        {{-- Dynamic Collection Names --}}
                        <template x-for="col in getLinkCollections()" :key="col.slug">
                            <button type="button" @click="setLinkMode(field.name, col.slug); closeSelect('mode-' + field.name)"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm text-left transition-colors duration-75 hover:bg-primary/10"
                                :class="getLinkMode(field.name) === col.slug ? 'text-primary font-semibold bg-primary/5' : 'text-gray-700'">
                                <span class="truncate" x-text="col.name"></span>
                                <svg x-show="getLinkMode(field.name) === col.slug" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-primary">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Collection Entry Item Picker (when a collection is selected) --}}
            <template x-if="getLinkMode(field.name) !== 'custom'">
                <div class="relative flex-1 min-w-0" @keydown="selectKeydown($event, 'entry-' + field.name)">
                    <button type="button" @click.stop="toggleSelect('entry-' + field.name)"
                         class="w-full flex items-center justify-between gap-2 bg-white border border-gray-300 text-sm rounded-lg px-3 h-10 cursor-pointer transition-shadow duration-150 hover:shadow-sm"
                         :class="getSelect('entry-' + field.name).open ? 'ring-2 ring-primary/30 border-primary shadow-sm' : 'shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)]'">
                         <span class="truncate" :class="getField(field.name) ? 'text-gray-900 font-medium' : 'text-gray-400'">
                             <template x-if="getField(field.name)">
                                 <span x-text="pages.find(p => p.route === getField(field.name))?.title || getField(field.name)"></span>
                             </template>
                             <template x-if="!getField(field.name)">
                                 <span x-text="'Select ' + (getLinkModeLabel(getLinkMode(field.name))) + '...'"></span>
                             </template>
                         </span>
                         <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 opacity-60 transition-transform duration-150" :class="getSelect('entry-' + field.name).open ? 'rotate-180' : ''">
                             <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                         </svg>
                    </button>
                    <div x-show="getSelect('entry-' + field.name).open" x-cloak
                         class="absolute z-50 top-full mt-1.5 left-0 right-0 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
                        @click.away="closeSelect('entry-' + field.name)">
                        <div class="flex items-center gap-2 px-3 py-1.5 border-b border-gray-200">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 opacity-50 text-gray-400">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                            </svg>
                            <input type="text" :id="'sel-search-entry-' + field.name"
                                @input="getSelect('entry-' + field.name).search = $event.target.value; getSelect('entry-' + field.name).highlight = 0"
                                @click.stop
                                placeholder="Search..."
                                class="w-full bg-transparent text-sm text-gray-900 placeholder:text-gray-400 outline-none border-0 focus:ring-0">
                        </div>
                        <div class="max-h-60 overflow-y-auto py-1 [scrollbar-width:thin]">
                            <template x-for="item in getLinkEntries(getLinkMode(field.name)).filter(p => !getSelect('entry-' + field.name).search || p.title.toLowerCase().includes(getSelect('entry-' + field.name).search.toLowerCase()) || p.route.toLowerCase().includes(getSelect('entry-' + field.name).search.toLowerCase()))" :key="item.id">
                                <button type="button" @click="linkFieldValue(field.name, item.route); closeSelect('entry-' + field.name)"
                                    class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm text-left transition-colors duration-75 hover:bg-primary/10"
                                    :class="getField(field.name) === item.route ? 'text-primary font-semibold bg-primary/5' : 'text-gray-700'">
                                    <span class="truncate" x-text="item.title"></span>
                                    <svg x-show="getField(field.name) === item.route" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-primary">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>
                            <template x-if="getLinkEntries(getLinkMode(field.name)).filter(p => !getSelect('entry-' + field.name).search || p.title.toLowerCase().includes(getSelect('entry-' + field.name).search.toLowerCase()) || p.route.toLowerCase().includes(getSelect('entry-' + field.name).search.toLowerCase())).length === 0">
                                <div class="px-3 py-6 text-sm text-gray-400 text-center">No items found in this collection</div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Custom URL input (when Custom is selected) --}}
            <template x-if="getLinkMode(field.name) === 'custom'">
                <input type="text"
                    :value="getField(field.name)"
                    @input="linkFieldValue(field.name, $event.target.value)"
                    placeholder="https://example.com or /my-page"
                    class="flex-1 min-w-0 rounded-lg border border-gray-300 bg-white px-3 h-10 text-sm text-text-primary shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </template>
        </div>
    </div>
</template>
