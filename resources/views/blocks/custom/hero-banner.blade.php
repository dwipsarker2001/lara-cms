@php
    $d = $data;
    $destTaxSlug = $d['destinationTaxonomy'] ?? '';
    $destinations = collect();

    if ($destTaxSlug && \Illuminate\Support\Facades\Schema::hasTable('taxonomies')) {
        $tax = \App\Models\Taxonomy::where('slug', $destTaxSlug)->first();
        if ($tax) {
            $destinations = $tax->terms()->orderBy('title')->get();
        }
    }

    $searchPlaceholder = !empty($d['searchPlaceholder']) ? $d['searchPlaceholder'] : 'Where do you want to go?';
    $datePlaceholder = !empty($d['datePlaceholder']) ? $d['datePlaceholder'] : 'Add dates';
@endphp

<section data-block="heroBanner" class="relative w-full overflow-hidden md:rounded-xl">
    <div class="relative h-[560px] md:h-[650px] max-h-[650px] md:mx-[5%] md:rounded-2xl overflow-hidden flex items-center justify-center">
        @if($d['backgroundImage'] ?? false)
            <img data-edit="backgroundImage" src="{{ $d['backgroundImage'] }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
        @else
            <div data-edit="backgroundImage" class="absolute inset-0 bg-gray-800"></div>
        @endif
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="relative z-10 flex flex-col items-center text-center px-6 py-20 max-w-4xl mx-auto">
            @if($d['badge'] ?? false)
                <span data-edit="badge" class="inline-flex items-center gap-2 rounded-full bg-orange-400 px-6 py-2 text-sm font-medium text-white mb-6">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    {{ $d['badge'] }}
                </span>
            @endif
            @if($d['headline'] ?? false)
                <h1 data-edit="headline" class="text-3xl md:text-5xl lg:text-6xl font-bold text-white italic leading-tight">{{ $d['headline'] }}</h1>
            @endif
            @if($d['description'] ?? false)
                <p data-edit="description" class="mt-6 text-sm md:text-base text-gray-200 max-w-2xl capitalize">{{ $d['description'] }}</p>
            @endif

            {{-- Custom Built Interactive Search Container --}}
            <div
                class="relative mt-10 w-full max-w-3xl text-left"
                x-data="{
                    openPopup: false,
                    destSearch: '',
                    selectedDestination: '{{ request('destination', '') }}',
                    selectedDestinationTitle: '',
                    selectedDate: '{{ request('date', '') }}',

                    currentYear: new Date().getFullYear(),
                    currentMonth: new Date().getMonth(),
                    monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'],

                    init() {
                        @if($destinations->isNotEmpty())
                            const currentSlug = '{{ request('destination') }}';
                            const found = @js($destinations).find(d => d.slug === currentSlug);
                            if (found) {
                                this.selectedDestinationTitle = found.title;
                            }
                        @endif

                        if (this.selectedDate) {
                            const parts = this.selectedDate.split('-');
                            if (parts.length === 3) {
                                this.currentYear = parseInt(parts[0]);
                                this.currentMonth = parseInt(parts[1]) - 1;
                            }
                        }
                    },

                    get daysInMonth() {
                        return new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    },

                    get prevMonthDays() {
                        return new Date(this.currentYear, this.currentMonth, 0).getDate();
                    },

                    get firstDayOfWeek() {
                        return new Date(this.currentYear, this.currentMonth, 1).getDay();
                    },

                    prevMonth() {
                        if (this.currentMonth === 0) {
                            this.currentMonth = 11;
                            this.currentYear--;
                        } else {
                            this.currentMonth--;
                        }
                    },

                    nextMonth() {
                        if (this.currentMonth === 11) {
                            this.currentMonth = 0;
                            this.currentYear++;
                        } else {
                            this.currentMonth++;
                        }
                    },

                    selectDate(day) {
                        const m = String(this.currentMonth + 1).padStart(2, '0');
                        const d = String(day).padStart(2, '0');
                        this.selectedDate = `${this.currentYear}-${m}-${d}`;
                    },

                    isSelectedDate(day) {
                        if (!this.selectedDate) return false;
                        const m = String(this.currentMonth + 1).padStart(2, '0');
                        const d = String(day).padStart(2, '0');
                        return this.selectedDate === `${this.currentYear}-${m}-${d}`;
                    },

                    isToday(day) {
                        const now = new Date();
                        return now.getFullYear() === this.currentYear &&
                               now.getMonth() === this.currentMonth &&
                               now.getDate() === day;
                    },

                    selectDestination(slug, title) {
                        this.selectedDestination = slug;
                        this.selectedDestinationTitle = title;
                    },

                    clearAll() {
                        this.selectedDestination = '';
                        this.selectedDestinationTitle = '';
                        this.selectedDate = '';
                        this.destSearch = '';
                    },

                    submitSearch() {
                        this.openPopup = false;
                        this.$nextTick(() => {
                            this.$refs.searchForm.submit();
                        });
                    }
                }"
            >
                <form
                    x-ref="searchForm"
                    action="{{ $d['searchUrl'] ?? '#' }}"
                    method="get"
                    class="relative flex flex-wrap sm:flex-nowrap items-center bg-white rounded-2xl sm:rounded-full shadow-2xl overflow-hidden p-2 gap-2 border border-gray-100 z-20"
                >
                    <input type="hidden" name="destination" :value="selectedDestination">
                    <input type="hidden" name="date" :value="selectedDate">

                    {{-- Destination Trigger --}}
                    <div
                        @click.stop="openPopup = true"
                        class="flex flex-1 items-center gap-3 px-4 py-2.5 rounded-xl sm:rounded-full hover:bg-gray-50 transition-colors cursor-pointer border-b sm:border-b-0 sm:border-r border-gray-200 min-w-[220px]"
                    >
                        <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span class="text-sm truncate min-w-0" :class="selectedDestinationTitle ? 'text-gray-800 font-medium' : 'text-gray-400 font-normal'" x-text="selectedDestinationTitle || {{ json_encode($searchPlaceholder) }}">{{ $searchPlaceholder }}</span>
                    </div>

                    {{-- Date Trigger --}}
                    <div
                        @click.stop="openPopup = true"
                        class="flex flex-1 items-center gap-3 px-4 py-2.5 rounded-xl sm:rounded-full hover:bg-gray-50 transition-colors cursor-pointer min-w-[180px]"
                    >
                        <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm truncate min-w-0" :class="selectedDate ? 'text-gray-800 font-medium' : 'text-gray-400 font-normal'" x-text="selectedDate || {{ json_encode($datePlaceholder) }}">{{ $datePlaceholder }}</span>
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        aria-label="Search"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-500 text-white transition-all hover:bg-orange-600 hover:scale-105 shadow-md shrink-0 cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>

                {{-- Custom-Built Pixel-Perfect Centered Modal Popup --}}
                <template x-teleport="body">
                    <div
                        x-show="openPopup"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-md p-4 sm:p-6"
                        x-cloak
                    >
                        <div
                            @click.outside="openPopup = false"
                            class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl border border-gray-100 p-6 text-left overflow-hidden max-h-[90vh] overflow-y-auto"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                                {{-- Left Column: Where to --}}
                                <div class="md:col-span-6 pr-0 md:pr-4 flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Where to</h3>

                                        {{-- Search Input Box --}}
                                        <div class="relative mb-3">
                                            <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg>
                                            <input
                                                type="text"
                                                x-model="destSearch"
                                                placeholder="{{ $d['searchPlaceholder'] ?? 'Where do you want to go?' }}"
                                                class="w-full pl-10 pr-3 py-2 text-sm bg-gray-50/60 border border-gray-200 rounded-lg text-gray-800 placeholder:text-gray-400 font-medium transition-all focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:bg-white"
                                            />
                                        </div>

                                        {{-- Scrollable Destination List --}}
                                        <div class="max-h-[280px] overflow-y-auto space-y-1.5 p-1 custom-scrollbar">
                                            @if($destinations->isNotEmpty())
                                                @foreach($destinations as $dest)
                                                    @php
                                                        $tData = $dest->data ?? [];
                                                        $imgUrl = $tData['image'] ?? $tData['icon'] ?? $tData['photo'] ?? $tData['featured_image'] ?? null;
                                                    @endphp
                                                    <div
                                                        x-show="!destSearch || '{{ strtolower(addslashes($dest->title)) }}'.includes(destSearch.toLowerCase())"
                                                        @click="selectDestination('{{ $dest->slug }}', '{{ addslashes($dest->title) }}')"
                                                        class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg cursor-pointer transition-all"
                                                        :class="selectedDestination === '{{ $dest->slug }}' ? 'bg-gray-100 text-gray-900 font-semibold border border-gray-200' : 'text-gray-700 hover:bg-gray-50 border border-transparent'"
                                                    >
                                                        @if($imgUrl)
                                                            <img src="{{ $imgUrl }}" alt="{{ $dest->title }}" class="size-8 rounded-md object-cover shrink-0 border border-gray-200" />
                                                        @else
                                                            <div class="size-8 rounded-md bg-gray-200 text-gray-700 flex items-center justify-center shrink-0 font-bold text-xs">
                                                                {{ strtoupper(substr($dest->title, 0, 2)) }}
                                                            </div>
                                                        @endif
                                                        <span class="text-sm" :class="selectedDestination === '{{ $dest->slug }}' ? 'text-gray-900 font-semibold' : 'text-gray-800 font-medium'">{{ $dest->title }}</span>
                                                        <template x-if="selectedDestination === '{{ $dest->slug }}'">
                                                            <svg class="w-4 h-4 ml-auto text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                                        </template>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="p-4 text-center text-xs text-gray-400 bg-gray-50 rounded-xl">
                                                    No destinations loaded.
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="pt-4 border-t border-gray-100">
                                        <button type="button" @click="clearAll()" class="text-xs text-gray-400 hover:text-gray-600 font-medium cursor-pointer">Clear all</button>
                                    </div>
                                </div>

                                {{-- Right Column: Custom Calendar --}}
                                <div class="md:col-span-6 pt-4 md:pt-0 md:pl-6 flex flex-col justify-between relative">
                                    {{-- Close X Button --}}
                                    <button type="button" @click="openPopup = false" class="absolute right-0 top-0 text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>

                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-700 mb-3">When do you want to go?</h3>

                                        {{-- Custom Month Header --}}
                                        <div class="flex items-center justify-between text-sm text-gray-800 mb-4 pb-2 border-b border-gray-100">
                                            <span x-text="monthNames[currentMonth] + ' ' + currentYear" class="text-sm font-semibold text-gray-800"></span>
                                            <div class="flex items-center gap-1">
                                                <button type="button" @click="prevMonth()" class="size-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors cursor-pointer" title="Previous month">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                                                </button>
                                                <button type="button" @click="nextMonth()" class="size-8 rounded-lg flex items-center justify-center text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors cursor-pointer" title="Next month">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Weekday Headers --}}
                                        <div class="grid grid-cols-7 text-center text-xs font-bold text-gray-400 mb-2">
                                            <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                        </div>

                                        {{-- Day Numbers Matrix --}}
                                        <div class="grid grid-cols-7 gap-y-1.5 text-center text-xs font-medium text-gray-700 py-1">
                                            {{-- Previous Month Fillers --}}
                                            <template x-for="i in firstDayOfWeek" :key="'blank-' + i">
                                                <span class="py-1 text-gray-300 text-[11px]" x-text="prevMonthDays - firstDayOfWeek + i"></span>
                                            </template>

                                            {{-- Current Month Days --}}
                                            <template x-for="day in daysInMonth" :key="'day-' + day">
                                                <button
                                                    type="button"
                                                    @click="selectDate(day)"
                                                    class="size-8 mx-auto flex items-center justify-center rounded-full transition-all cursor-pointer text-xs font-semibold"
                                                    :class="{
                                                        'bg-emerald-600 text-white font-bold shadow-md scale-105': isSelectedDate(day),
                                                        'ring-1 ring-emerald-500 text-emerald-600 font-bold': isToday(day) && !isSelectedDate(day),
                                                        'text-gray-700 hover:bg-emerald-50 hover:text-emerald-600': !isSelectedDate(day) && !isToday(day)
                                                    }"
                                                    x-text="day"
                                                ></button>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Bottom Action Buttons --}}
                                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                                        <button type="button" @click="openPopup = false" class="text-xs font-semibold text-gray-500 hover:text-gray-700 px-3 py-2 cursor-pointer">
                                            Cancel
                                        </button>
                                        <button type="button" @click="submitSearch()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-5 py-2.5 rounded-full shadow-md transition-colors cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                            <span>Search</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</section>
