<div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm py-4 flex flex-col min-h-0 flex-1"
     x-data="{
        notifications: {{ json_encode($updates) }},
        period: 'Today',
        searchQuery: '',
        displayLimit: 8,
        init() {
            this.$watch('period', () => { this.displayLimit = 8; });
            this.$watch('searchQuery', () => { this.displayLimit = 8; });
        },
        get matchingNotifications() {
            return this.notifications.filter(item => {
                let matchesPeriod = false;
                if (this.period === 'Today') {
                    matchesPeriod = item.period === 'Today';
                } else if (this.period === 'Yesterday') {
                    matchesPeriod = item.period === 'Yesterday';
                } else if (this.period === 'This week') {
                    matchesPeriod = ['Today', 'Yesterday', 'This week'].includes(item.period);
                }

                if (!matchesPeriod) return false;

                if (!this.searchQuery) return true;
                const query = this.searchQuery.toLowerCase();
                return item.title.toLowerCase().includes(query) || item.sub.toLowerCase().includes(query);
            });
        },
        get visibleNotifications() {
            return this.matchingNotifications.slice(0, this.displayLimit);
        },
        get filteredCount() {
            return this.matchingNotifications.length;
        },
        get pendingCount() {
            return this.matchingNotifications.filter(item => item.tone === 'text-red-500' || item.tone === 'text-amber-500').length;
        },
        handleScroll(e) {
            const el = e.target;
            const scrollBottom = el.scrollHeight - el.scrollTop - el.clientHeight;
            if (scrollBottom <= 80) {
                if (this.displayLimit < this.matchingNotifications.length) {
                    this.displayLimit += 8;
                }
            }
        }
     }">
    <style>
        .notification-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .notification-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .notification-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 9999px;
            transition: background-color 0.2s ease;
        }
        .notification-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>

    <div class="px-4">
        <div class="flex gap-1 rounded-lg bg-gray-100/90 p-1">
            @foreach (['Today', 'Yesterday', 'This week'] as $opt)
                <button
                    type="button"
                    @click="period = '{{ $opt }}'"
                    class="flex-1 rounded-md px-2 py-1.5 text-[12px] font-medium transition-all duration-150 cursor-pointer select-none"
                    :class="period === '{{ $opt }}' ? 'bg-white text-text-heading shadow-xs font-semibold' : 'text-text-muted hover:text-text-heading hover:bg-white/40'"
                >{{ $opt }}</button>
            @endforeach
        </div>
    </div>

    <div class="relative mt-3 px-4">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 absolute left-7 top-1/2 -translate-y-1/2 text-text-muted/70"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
        <input x-model="searchQuery" placeholder="Search notifications..." class="w-full rounded-lg border border-content-border bg-white py-2 pl-9 pr-3 text-[12.5px] text-text-heading placeholder:text-text-muted/60 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition-all shadow-2xs">
    </div>

    <div class="mt-3 px-4 flex items-center justify-between gap-3 text-[12px] text-text-muted pb-1" x-show="filteredCount > 0">
        <p class="flex items-center gap-1.5 font-medium">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 text-primary shrink-0">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <span><span class="font-semibold text-text-heading" x-text="filteredCount">8</span> new updates</span>
        </p>
        <template x-if="pendingCount > 0">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[11px] font-medium border border-amber-200/60">
                <span class="size-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span x-text="pendingCount">3</span> pending
            </span>
        </template>
    </div>

    <ul class="mt-1 flex-1 min-h-0 max-h-[380px] divide-y divide-gray-100 overflow-y-auto notification-scrollbar"
        x-show="filteredCount > 0"
        @scroll.passive="handleScroll($event)">
        <template x-for="(u, index) in visibleNotifications" :key="index">
            <li class="flex items-start gap-3 py-3 px-4 hover:bg-gray-50/90 transition-colors group cursor-pointer">
                <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-xl bg-gray-100/80 group-hover:scale-105 transition-transform"
                    :class="{
                        'bg-blue-50 text-blue-600': u.icon === 'user-plus',
                        'bg-emerald-50 text-emerald-600': u.icon === 'comments',
                        'bg-red-50 text-red-600': u.icon === 'triangle-exclamation' && u.tone.includes('red'),
                        'bg-amber-50 text-amber-600': u.icon === 'triangle-exclamation' && !u.tone.includes('red'),
                        'bg-indigo-50 text-indigo-600': u.icon === 'book-open',
                        'bg-purple-50 text-purple-600': u.icon === 'star'
                    }">
                    <template x-if="u.icon === 'user-plus'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="8.5" cy="7" r="4" /><line x1="20" y1="8" x2="20" y2="14" /><line x1="23" y1="11" x2="17" y2="11" /></svg>
                    </template>
                    <template x-if="u.icon === 'comments'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" /></svg>
                    </template>
                    <template x-if="u.icon === 'triangle-exclamation'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /><line x1="12" y1="9" x2="12" y2="13" /><line x1="12" y1="17" x2="12.01" y2="17" /></svg>
                    </template>
                    <template x-if="u.icon === 'book-open'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z" /><path d="M22 3h-6a4 4 0 013-3h7z" /></svg>
                    </template>
                    <template x-if="u.icon === 'star'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" /></svg>
                    </template>
                    <template x-if="!['user-plus', 'comments', 'triangle-exclamation', 'book-open', 'star'].includes(u.icon)">
                        <i :class="`fa-solid fa-${u.icon} text-[14px]`"></i>
                    </template>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <span class="truncate text-[13px] font-semibold text-text-heading group-hover:text-primary transition-colors" x-text="u.title"></span>
                        <span class="shrink-0 text-[11px] font-medium text-text-muted/80 bg-gray-100/70 px-2 py-0.5 rounded-full" x-text="u.time"></span>
                    </div>
                    <p class="mt-0.5 truncate text-[12px] text-text-muted leading-relaxed" x-text="u.sub"></p>
                </div>
            </li>
        </template>
        <li x-show="displayLimit < matchingNotifications.length" class="py-3 text-center text-xs text-text-muted font-medium flex items-center justify-center gap-2">
            <svg class="animate-spin size-3.5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Loading more updates...</span>
        </li>
    </ul>

    <div x-show="filteredCount === 0" class="mt-6 mx-4 text-center py-8 px-4 rounded-xl bg-gray-50/60 border border-dashed border-gray-200">
        <div class="size-10 rounded-full bg-white ring-1 ring-gray-200 shadow-2xs mx-auto flex items-center justify-center mb-2.5">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-5 text-text-muted/60"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 0 1-3.46 0" /></svg>
        </div>
        <p class="text-[13px] font-medium text-text-heading">No notifications found</p>
        <p class="text-[11.5px] text-text-muted mt-0.5">Check back later or try adjusting your filter</p>
    </div>
</div>
