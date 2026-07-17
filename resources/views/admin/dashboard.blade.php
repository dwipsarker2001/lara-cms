@extends('admin.layout')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@php
$stats = [
    (object)[
        'label' => 'Current Tickets',
        'value' => '3,484',
        'delta' => '+7.1%',
        'up' => true,
        'data' => [12, 18, 10, 22, 16, 26, 20, 30, 24, 34],
    ],
    (object)[
        'label' => 'Daily Avg. Resolution',
        'value' => '486',
        'delta' => '+2%',
        'up' => true,
        'data' => [20, 16, 24, 18, 28, 22, 26, 21, 30, 27],
    ],
    (object)[
        'label' => 'SLA Compliance Rate',
        'value' => '92%',
        'delta' => '-1.3%',
        'up' => false,
        'data' => [28, 24, 30, 22, 26, 18, 24, 16, 22, 14],
    ],
];
$volume = [
    (object)['day' => 'Sun', 'v' => 430],
    (object)['day' => 'Mon', 'v' => 300],
    (object)['day' => 'Tue', 'v' => 584],
    (object)['day' => 'Wed', 'v' => 500],
    (object)['day' => 'Thu', 'v' => 660],
    (object)['day' => 'Fri', 'v' => 640],
    (object)['day' => 'Sat', 'v' => 360],
];
$maxVolume = 800;
$updates = [
    (object)['title' => 'New Client Added', 'sub' => 'PT. Alpha Indonesia registered', 'time' => '11:15 AM', 'icon' => 'user-plus', 'tone' => 'text-text-muted'],
    (object)['title' => 'Agent Reassigned', 'sub' => 'Ticket #2322 moved to Michael Wong', 'time' => '11:00 AM', 'icon' => 'comments', 'tone' => 'text-text-muted'],
    (object)['title' => 'SLA Breach Risk', 'sub' => "Ticket #2320 'Login issue'", 'time' => '10:45 AM', 'icon' => 'triangle-exclamation', 'tone' => 'text-red-500'],
    (object)['title' => 'Knowledge Base', 'sub' => "New article published: 'Login Troubleshooting'", 'time' => '10:30 AM', 'icon' => 'book-open', 'tone' => 'text-text-muted'],
    (object)['title' => 'Customer Feedback', 'sub' => "'Great support response, thanks Sarah!'", 'time' => '10:30 AM', 'icon' => 'star', 'tone' => 'text-amber-500'],
];
$rows = [
    (object)['id' => '#2319', 'subject' => 'Payment failed on invoice', 'priority' => 'High', 'agent' => 'John Doe', 'status' => 'In Review', 'created' => '2025-08-18', 'due' => '2h left'],
    (object)['id' => '#2320', 'subject' => 'Login issue', 'priority' => 'Medium', 'agent' => 'Sarah Lee', 'status' => 'Delivered', 'created' => '2025-08-19', 'due' => '1h left'],
    (object)['id' => '#2321', 'subject' => 'Feature request export', 'priority' => 'Low', 'agent' => 'John Doe', 'status' => 'In Progress', 'created' => '2025-08-19', 'due' => '1d left'],
    (object)['id' => '#2322', 'subject' => 'Contract renewal issue', 'priority' => 'Medium', 'agent' => 'Michael Wong', 'status' => 'In Progress', 'created' => '2025-08-20', 'due' => '9h left'],
];
$agentPhotos = [
    'John Doe' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=80&h=80&fit=crop&crop=face',
    'Sarah Lee' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&h=80&fit=crop&crop=face',
    'Michael Wong' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face',
];
$avatarColors = ['bg-indigo-500', 'bg-rose-500', 'bg-amber-500', 'bg-sky-500'];
$initials = fn($name) => strtoupper(substr(implode('', array_map(fn($p) => $p[0] ?? '', explode(' ', $name))), 0, 2));
@endphp

@section('content')
    <div x-data="{ period: 'Today' }">
        <header class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-[25px] font-semibold text-text-heading">Hello, Samantha Walker 👋</h1>
                <p class="mt-1 text-[14px] text-text-muted">Here are the latest insights from your customer interactions.</p>
            </div>
            <div class="flex items-center gap-2">
                <x-admin::modern-dropdown value="Last week">
                    <button type="button" @click="selected = 'Last week'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selected === 'Last week' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">Last week</button>
                    <button type="button" @click="selected = 'This week'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selected === 'This week' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">This week</button>
                    <button type="button" @click="selected = 'This month'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selected === 'This month' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">This month</button>
                </x-admin::modern-dropdown>
                <button class="flex size-9 items-center justify-center rounded-lg border border-content-border bg-white text-text-muted shadow-sm hover:bg-gray-50 cursor-pointer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><circle cx="12" cy="12" r="1" /><circle cx="19" cy="12" r="1" /><circle cx="5" cy="12" r="1" /></svg>
                </button>
            </div>
        </header>

        <div class="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-4">
            <div class="flex flex-col gap-4 lg:col-span-3">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ($stats as $s)
                        <div class="bg-gray-100 rounded-2xl p-2">
                            <div class="flex items-center justify-between px-2 pb-2.5">
                                <span class="text-[13px] font-medium text-text-muted">{{ $s->label }}</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 text-text-muted shrink-0">
                                    @if ($loop->index === 0)
                                        <rect x="2" y="6" width="20" height="12" rx="2" /><path d="M12 12h.01" />
                                    @elseif ($loop->index === 1)
                                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" />
                                    @else
                                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 17" /><polyline points="17 6 23 6 23 12" />
                                    @endif
                                </svg>
                            </div>
                            <div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-4">
                                <div class="flex items-end justify-between gap-2">
                                    <div>
                                        <div class="text-[26px] font-semibold leading-none text-text-heading">{{ $s->value }}</div>
                                        <div class="mt-2 flex items-center gap-1 text-[12px]">
                                            <span class="{{ $s->up ? 'font-medium text-emerald-600' : 'font-medium text-red-500' }}">{{ $s->delta }}</span>
                                            <span class="text-text-muted">vs last week</span>
                                        </div>
                                    </div>
                                    <div class="h-10 w-24">
                                        <svg viewBox="0 0 100 40" class="w-full h-full" preserveAspectRatio="none">
                                            <defs>
                                                <linearGradient id="g-{{ $loop->index }}" x1="0" y1="0" x2="0" y2="1">
                                                    <stop offset="0%" stop-color="{{ $s->up ? '#10b981' : '#ef4444' }}" stop-opacity="0.25" />
                                                    <stop offset="100%" stop-color="{{ $s->up ? '#10b981' : '#ef4444' }}" stop-opacity="0" />
                                                </linearGradient>
                                            </defs>
                                            @php
                                                $pts = $s->data;
                                                $min = min($pts);
                                                $max = max($pts);
                                                $range = $max - $min ?: 1;
                                                $w = 100;
                                                $h = 40;
                                                $step = $w / (count($pts) - 1);
                                                $points = implode(' ', array_map(fn($i, $v) => round($step * $i, 1).','.round($h - (($v - $min) / $range) * $h, 1), array_keys($pts), $pts));
                                            @endphp
                                            <path d="M0,40 L{{ $points }} L100,40 Z" fill="url(#g-{{ $loop->index }})" />
                                            <polyline fill="none" stroke="{{ $s->up ? '#10b981' : '#ef4444' }}" stroke-width="1.75" points="{{ $points }}" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-gray-100 rounded-2xl p-2">
                    <div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-[14px] font-medium text-text-heading">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-text-muted"><rect x="4" y="4" width="16" height="16" rx="2" /><path d="M7 15l3-3 3 3 4-4" /></svg>
                                Ticket Volume Trend
                            </div>
                            <x-admin::modern-dropdown value="Last week">
                                <button type="button" @click="selected = 'Last week'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selected === 'Last week' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">Last week</button>
                                <button type="button" @click="selected = 'This week'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selected === 'This week' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">This week</button>
                                <button type="button" @click="selected = 'This month'; open = false" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="selected === 'This month' ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">This month</button>
                            </x-admin::modern-dropdown>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="text-[28px] font-semibold leading-none text-text-heading">4,790</span>
                            <span class="text-[12px] font-medium text-emerald-600">+8%</span>
                            <span class="text-[12px] text-text-muted">vs last week</span>
                        </div>
                        <div class="mt-4 h-60 flex items-end gap-2">
                            @foreach ($volume as $d)
                                <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end">
                                    <div class="w-full relative" style="height: calc(100% - 24px)">
                                        <div class="absolute inset-x-0 bottom-0 rounded-t-md transition-all" style="height: {{ ($d->v / $maxVolume) * 100 }}%; {{ $d->day === 'Tue' ? 'background-color: #27272a;' : 'background-color: #eceef1;' }}"></div>
                                    </div>
                                    <span class="text-[12px] text-text-muted">{{ $d->day }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-100 rounded-2xl p-2 h-full">
                <div class="flex items-center justify-between px-2 pb-2.5">
                    <span class="text-[14px] font-medium text-text-heading">Latest Updates</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-text-muted"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><polyline points="14 2 14 8 20 8" /></svg>
                </div>
                <div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-4 flex flex-col">
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
                    <p class="mt-3 text-[12px] text-text-muted"><span class="font-medium text-text-heading">8</span> new activities today</p>
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
            </div>
        </div>

        {{-- SLA Monitoring --}}
        <div class="bg-gray-100 rounded-2xl p-2 mb-1">
            <div class="flex items-center justify-between px-4 pb-3">
                <span class="flex items-center gap-2 text-[14px] font-medium text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-text-muted"><circle cx="12" cy="12" r="4" /><path d="M16 8v5a3 3 0 006 0v-1a10 10 0 10-3.92 7.94" /></svg>
                    SLA Monitoring
                </span>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-text-muted"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
                        <input type="text" placeholder="Ticket" class="h-8 w-40 rounded-lg border border-content-border bg-white pl-8 pr-3 text-[12px] text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/10 shadow-sm">
                    </div>
                    <button class="flex h-8 items-center gap-1.5 rounded-lg border border-content-border bg-white px-3 text-[12px] font-medium text-text-heading hover:bg-gray-50 shadow-sm transition-colors cursor-pointer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 text-text-muted"><polygon points="22 3 22 7 14 15 14 21 10 18 10 15 2 7 2 3 22 3" /></svg>
                        Filter
                    </button>
                    <button class="flex size-8 items-center justify-center rounded-lg border border-content-border bg-white text-text-muted hover:bg-gray-50 shadow-sm transition-colors cursor-pointer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><circle cx="12" cy="12" r="1" /><circle cx="19" cy="12" r="1" /><circle cx="5" cy="12" r="1" /></svg>
                    </button>
                </div>
            </div>
            <div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-[5px]">
                <div class="overflow-x-auto">
                    <table class="w-full border-separate border-spacing-y-0 text-left text-[13px]">
                        <thead>
                            <tr class="bg-[#f9fafb]">
                                <th class="w-12 rounded-l-xl px-5 py-3"><input type="checkbox" class="size-4 rounded border-gray-300 bg-white text-zinc-900 accent-zinc-900 focus:ring-0 cursor-pointer"></th>
                                <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                                    <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Ticket ID <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                                    <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Subject <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                                    <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Priority <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                                    <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Assigned To <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                                    <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Status <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                                    <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Created Date <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] rounded-r-xl">
                                    <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">SLA Due <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                                </th>
                                <th class="w-4 pr-2"></th>
                            </tr>
                            <tr class="h-2"><td colspan="9"></td></tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $i => $row)
                                @php
                                    $barsCount = $row->priority === 'High' ? 3 : ($row->priority === 'Medium' ? 2 : 1);
                                    $barColor = $row->priority === 'High' ? 'bg-red-500' : ($row->priority === 'Medium' ? 'bg-amber-500' : 'bg-yellow-500');
                                    $statusIcon = $row->status === 'In Review' ? 'file-lines' : ($row->status === 'Delivered' ? 'check' : 'clock');
                                    $statusColor = $row->status === 'In Review' ? 'text-blue-500' : ($row->status === 'Delivered' ? 'text-emerald-500' : 'text-amber-500');
                                @endphp
                                <tr class="group transition-colors hover:bg-gray-50/50">
                                    <td class="border-b border-gray-100 bg-white px-5 py-3 {{ $i === 0 ? 'rounded-tl-xl' : '' }} {{ $i === count($rows) - 1 ? 'rounded-bl-xl' : '' }}">
                                        <input type="checkbox" {{ $i === 0 ? 'checked' : '' }} class="size-4 rounded border-gray-300 bg-white text-zinc-900 accent-zinc-900 focus:ring-0 cursor-pointer">
                                    </td>
                                    <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $row->id }}</td>
                                    <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 text-gray-900">{{ $row->subject }}</td>
                                    <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 text-gray-900">
                                        <span class="inline-flex items-center gap-2 text-gray-900">
                                            <span class="inline-flex items-end gap-0.5 h-3.5 mb-0.5">
                                                @foreach ([0, 1, 2] as $b)
                                                    <span class="w-0.5 rounded-sm {{ $b < $barsCount ? $barColor : 'bg-gray-200' }}" style="height: {{ [1.5, 2.5, 3.5][$b] * 4 }}px;"></span>
                                                @endforeach
                                            </span>
                                            <span class="text-[13px]">{{ $row->priority }}</span>
                                        </span>
                                    </td>
                                    <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3">
                                        <span class="inline-flex items-center gap-2.5 text-gray-900">
                                            @if (isset($agentPhotos[$row->agent]))
                                                <img src="{{ $agentPhotos[$row->agent] }}" alt="{{ $row->agent }}" class="size-6 rounded-full object-cover ring-1 ring-gray-100">
                                            @else
                                                <span class="flex size-6 items-center justify-center rounded-full text-[10px] font-medium text-white {{ $avatarColors[$i % count($avatarColors)] }}">{{ $initials($row->agent) }}</span>
                                            @endif
                                            <span class="text-[13px]">{{ $row->agent }}</span>
                                        </span>
                                    </td>
                                    <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3">
                                        <span class="inline-flex items-center gap-2.5 text-gray-900">
                                            @if ($row->status === 'In Review')
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 text-blue-500 shrink-0"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><polyline points="14 2 14 8 20 8" /><line x1="16" y1="13" x2="8" y2="13" /><line x1="16" y1="17" x2="8" y2="17" /></svg>
                                            @elseif ($row->status === 'Delivered')
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 text-emerald-500 shrink-0"><polyline points="20 6 9 17 4 12" /></svg>
                                            @elseif ($row->status === 'In Progress')
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 text-amber-500 shrink-0"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                                            @endif
                                            <span class="text-[13px]">{{ $row->status }}</span>
                                        </span>
                                    </td>
                                    <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 text-gray-400">{{ $row->created }}</td>
                                    <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 text-gray-900 font-medium">{{ $row->due }}</td>
                                    <td class="border-b border-gray-100 bg-white px-4 py-3 pr-5 text-right {{ $i === 0 ? 'rounded-tr-xl' : '' }} {{ $i === count($rows) - 1 ? 'rounded-br-xl' : '' }}">
                                        <button class="inline-flex size-7 items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100/80 transition-colors cursor-pointer">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><circle cx="12" cy="12" r="1" /><circle cx="19" cy="12" r="1" /><circle cx="5" cy="12" r="1" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
