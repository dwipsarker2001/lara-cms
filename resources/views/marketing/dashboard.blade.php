@extends('marketing.layouts.app')

@section('content')
@php
    $campaignPct  = $allCampaignNum  != 0 ? round($campaignNum  / $allCampaignNum  * 100) : 0;
    $contactPct   = $allContactNum   != 0 ? round($contactsNum  / $allContactNum   * 100) : 0;
    $templatePct  = $allTemplateNum  != 0 ? round($templatesNum / $allTemplateNum  * 100) : 0;
    $openPct      = $total           != 0 ? round($opened       / $total           * 100) : 0;
    $clickPct     = $total           != 0 ? round($clicked      / $total           * 100) : 0;
    $unsubPct     = $total           != 0 ? round($blackList    / $total           * 100) : 0;

    $unread  = max(0, $total - $opened);
    $hasData = ($total > 0 || $campaignNum > 0 || $contactsNum > 0 || $templatesNum > 0);
@endphp

<div class="w-full min-h-screen bg-slate-50/50 p-6 text-slate-900">
    <div class="mx-auto">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-14">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    <span id="greeting-text">Hello</span>, {{ $profile->firstname ?: $profile->username }}
                </h1>
                <p class="text-slate-500 text-sm mt-1">Here's what's happening with your account today.</p>
            </div>

            {{-- Dynamic Filter Switch --}}
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 mr-2">
                    {{ $filter === 'weekly' ? 'Last 7 days' : 'Last 30 days' }}
                </span>
                <div class="inline-flex rounded-lg border border-slate-200 bg-white p-1 shadow-sm">
                    <a href="?filter=monthly"
                       class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all
                              {{ $filter === 'monthly' ? 'text-teal-700 bg-teal-50' : 'text-slate-600 hover:bg-slate-50' }}">
                        Last 30 days
                    </a>
                    <a href="?filter=weekly"
                       class="px-3 py-1.5 text-xs font-semibold rounded-md transition-all
                              {{ $filter === 'weekly' ? 'text-teal-700 bg-teal-50' : 'text-slate-600 hover:bg-slate-50' }}">
                        Weekly
                    </a>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-800">Account Statistics</h2>
            <a href="{{ url('../pricing') }}" class="text-sm font-medium text-teal-600 hover:text-teal-700 inline-flex items-center gap-1 transition-colors">
                Go to the report page to see more instances.
                <i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>
            </a>
        </div>

        @if($hasData)
        {{-- DATA EXISTS --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-7">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Campaigns --}}
                    <a href="{{ route('app.campaign.index')}}" class="bg-white p-6 rounded-xl transition hover:border-slate-300 border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Campaigns</h3>
                            <i class="hgi hgi-stroke hgi-megaphone-03 text-slate-400 text-lg"></i>
                        </div>
                        <div class="flex items-end justify-between">
                            <div>
                                <span class="text-3xl font-black text-slate-900">{{ $campaignNum }}</span>
                                <p class="text-[12px] text-slate-500 mt-1 font-medium">Used: {{ $campaignNum }} / {{ $allCampaignNum }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                                {{ $campaignPct }}%
                            </span>
                        </div>
                    </a>

                    {{-- Contacts --}}
                    <a href="{{ route('app.group.index')}}" class="bg-white p-6 rounded-xl border hover:border-slate-300 transition border-slate-200 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Contacts</h3>
                            <i class="hgi hgi-stroke hgi-user-group text-slate-400 text-lg"></i>
                        </div>
                        <div class="flex items-end justify-between">
                            <div>
                                <span class="text-3xl font-black text-slate-900">{{ $contactsNum }}</span>
                                <p class="text-[12px] text-slate-500 mt-1 font-medium">Total Limit: {{ $allContactNum }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                                {{ $contactPct }}%
                            </span>
                        </div>
                    </a>

                    {{-- Emails Opened --}}
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Emails Opened</h3>
                            <i class="hgi hgi-stroke hgi-mail-open-01 text-emerald-500 text-lg"></i>
                        </div>
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-black text-slate-900">{{ $opened }}</span>
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                                {{ $openPct }}%
                            </span>
                        </div>
                    </div>

                    {{-- Total Clicks --}}
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Clicks</h3>
                            <i class="hgi hgi-stroke hgi-cursor-pointer-01 text-blue-500 text-xl"></i>
                        </div>
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-black text-slate-900">{{ $clicked }}</span>
                            <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100">
                                {{ $clickPct }}%
                            </span>
                        </div>
                    </div>

                    {{-- Unsubscribed --}}
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Unsubscribed</h3>
                            <i class="hgi hgi-stroke hgi-user-block-01 text-red-500 text-lg"></i>
                        </div>
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-black text-slate-900">{{ $blackList }}</span>
                            <span class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-bold border border-red-100">
                                {{ $unsubPct }}%
                            </span>
                        </div>
                    </div>

                    {{-- Templates --}}
                    <a href="{{ route('app.template.index')}}" class="bg-white p-6 rounded-xl border hover:border-slate-300 transition border-slate-200 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Templates</h3>
                            <i class="hgi hgi-stroke hgi-layout-01 text-slate-400 text-lg"></i>
                        </div>
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-black text-slate-900">{{ $templatesNum }}</span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                                {{ $templatePct }}%
                            </span>
                        </div>
                    </a>

                </div>
            </div>

            {{-- Engagement Chart --}}
            <div class="lg:col-span-5">
                <a href="{{ route('app.report.index')}}" class="bg-white px-8 pt-8 hover:border-slate-300 rounded-xl border border-slate-200 shadow-sm h-full flex flex-col">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Engagement Analysis</h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $filter === 'weekly' ? 'Last 7 days' : 'Last 30 days' }}
                            </p>
                        </div>
                        <i class="hgi hgi-stroke hgi-analytics-01 text-slate-400"></i>
                    </div>
                    <div class="relative h-[210px] mb-8">
                        <canvas id="engagementChart"></canvas>
                    </div>
                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-100">
                        <div class="text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Open Rate</p>
                            <h5 class="text-lg font-black text-emerald-600">{{ $openPct }}%</h5>
                        </div>
                        <div class="text-center border-x border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Click Rate</p>
                            <h5 class="text-lg font-black text-blue-600">{{ $clickPct }}%</h5>
                        </div>
                        <div class="text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Unsub</p>
                            <h5 class="text-lg font-black text-red-600">{{ $unsubPct }}%</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        @else
        {{-- NO DATA: empty state --}}
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <svg class="w-40 h-40 mb-6 text-slate-200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="90" fill="#f1f5f9"/>
                <rect x="55" y="65" width="90" height="70" rx="8" fill="#e2e8f0"/>
                <rect x="65" y="78" width="50" height="6" rx="3" fill="#cbd5e1"/>
                <rect x="65" y="92" width="70" height="6" rx="3" fill="#cbd5e1"/>
                <rect x="65" y="106" width="40" height="6" rx="3" fill="#cbd5e1"/>
                <rect x="115" y="95" width="34" height="26" rx="4" fill="#94a3b8"/>
                <polyline points="115,95 132,110 149,95" fill="none" stroke="#e2e8f0" stroke-width="2"/>
            </svg>
            <h3 class="text-xl font-bold text-slate-700 mb-2">No data yet</h3>
            <p class="text-slate-400 text-sm max-w-sm mb-8">
                Your dashboard will come alive once you create a campaign or add some contacts. Let's get started!
            </p>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ route('app.campaign.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    <i class="hgi hgi-stroke hgi-megaphone-03 text-base"></i>
                    Create your first campaign
                </a>
                <a href="{{ route('app.group.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg border border-slate-200 transition-colors shadow-sm">
                    <i class="hgi hgi-stroke hgi-user-add-01 text-base"></i>
                    Add contacts
                </a>
            </div>
        </div>
        @endif

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Greeting using browser local time
    const hour = new Date().getHours();
    let greeting = 'Good evening';
    if (hour < 12)      greeting = 'Good morning';
    else if (hour < 17) greeting = 'Good afternoon';
    document.getElementById('greeting-text').textContent = greeting;

    // Engagement Chart
    const chartCanvas = document.getElementById('engagementChart');
    if (!chartCanvas) return;

    const opened    = {{ $opened ?? 0 }};
    const clicked   = {{ $clicked ?? 0 }};
    const blackList = {{ $blackList ?? 0 }};
    const unread    = {{ $unread ?? 0 }};

    const allZero = (opened + clicked + blackList + unread) === 0;

    new Chart(chartCanvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: allZero ? ['No data'] : ['Opened', 'Clicked', 'Unsubscribed', 'Unread'],
            datasets: [{
                data: allZero ? [1] : [opened, clicked, blackList, unread],
                backgroundColor: allZero ? ['#e2e8f0'] : ['#10b981', '#3b82f6', '#ef4444', '#f1f5f9'],
                borderWidth: 0,
                hoverOffset: allZero ? 0 : 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '82%',
            plugins: {
                tooltip: { enabled: !allZero },
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 25,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 11, weight: '600', family: "'Inter', sans-serif" },
                        color: '#64748b'
                    }
                }
            }
        }
    });
});
</script>
@endsection