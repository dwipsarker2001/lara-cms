<div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-4 flex flex-col min-h-0 flex-1">
    <div class="flex gap-1 rounded-lg bg-gray-100 p-1">
        @foreach (['Today', 'Yesterday', 'This week'] as $opt)
            <button
                @click="period = '{{ $opt }}'"
                class="flex-1 rounded-md px-2 py-1.5 text-[12px] font-medium transition-colors"
                :class="period === '{{ $opt }}' ? 'bg-white text-text-heading shadow-sm' : 'text-text-muted hover:text-text-heading'"
            >{{ $opt }}</button>
        @endforeach
    </div>
    <div class="relative mt-3">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-text-muted"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
        <input placeholder="Search activities" class="w-full rounded-lg border border-content-border bg-white py-2 pl-9 pr-3 text-[13px] text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/20">
    </div>
    <div class="mt-3 flex gap-4 text-[12px] text-text-muted">
        <p><span class="font-medium text-text-heading">8</span> new activities today</p>
        <p><span class="font-medium text-text-heading">3</span> pending reviews</p>
    </div>
    <ul class="mt-2 flex-1 divide-y divide-content-border">
        @foreach ($updates as $u)
            <li class="flex items-start gap-3 py-3">
                <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-gray-100">
                    @php $iconName = $u->icon; @endphp
                    @if ($iconName === 'user-plus')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 {{ $u->tone }}"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="8.5" cy="7" r="4" /><line x1="20" y1="8" x2="20" y2="14" /><line x1="23" y1="11" x2="17" y2="11" /></svg>
                    @elseif ($iconName === 'comments')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 {{ $u->tone }}"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" /></svg>
                    @elseif ($iconName === 'triangle-exclamation')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 {{ $u->tone }}"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /><line x1="12" y1="9" x2="12" y2="13" /><line x1="12" y1="17" x2="12.01" y2="17" /></svg>
                    @elseif ($iconName === 'book-open')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 {{ $u->tone }}"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z" /><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z" /></svg>
                    @elseif ($iconName === 'star')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 {{ $u->tone }}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" /></svg>
                    @endif
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <span class="truncate text-[13px] font-medium text-text-heading">{{ $u->title }}</span>
                        <span class="shrink-0 text-[11px] text-text-muted">{{ $u->time }}</span>
                    </div>
                    <p class="mt-0.5 truncate text-[12px] text-text-muted">{{ $u->sub }}</p>
                </div>
            </li>
        @endforeach
    </ul>
</div>
