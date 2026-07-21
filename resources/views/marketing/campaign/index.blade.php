@extends('marketing.layouts.app')


@section('content')
<div class="w-full min-h-screen bg-slate-50/50 px-8 py-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Campaigns</h1>
            <p class="text-slate-500 mt-1.5">Create, manage, and monitor your email marketing performance.</p>
        </div>
        
        <button onclick="openCreateModal()" 
            class="flex items-center gap-3 px-6 py-2 rounded-lg text-white 
                bg-gradient-to-br from-[#007682] to-[#408b86]
                hover:brightness-110
                transition-all duration-300 active:scale-95
                shadow-lg">
            
            <i class="hgi hgi-stroke hgi-mail-add-02 text-lg"></i>
            <span class="font-bold text-sm">Create New Campaign</span>
        </button>
    </div>

    @php
        $grandTotalSent    = 0;
        $grandTotalOpened  = 0;
        $grandTotalClicked = 0;
        $grandTotalUnsubs  = 0;

        foreach($data as $campaign) {
            foreach($campaign->stats as $stat) {
                $grandTotalSent    += ($stat->total_sent  != '' ? count(explode(',', $stat->total_sent))  : 0);
                $grandTotalOpened  += ($stat->opened      != '' ? count(explode(',', $stat->opened))      : 0);
                $grandTotalClicked += ($stat->clicked     != '' ? count(explode(',', $stat->clicked))     : 0);
                $grandTotalUnsubs  += ($stat->black_list  != '' ? count(explode(',', $stat->black_list))  : 0);
            }
        }

        $avgOpenRate  = $grandTotalSent > 0 ? round(($grandTotalOpened  / $grandTotalSent) * 100, 1) : 0;
        $avgClickRate = $grandTotalSent > 0 ? round(($grandTotalClicked / $grandTotalSent) * 100, 1) : 0;
        $avgUnsubRate = $grandTotalSent > 0 ? round(($grandTotalUnsubs  / $grandTotalSent) * 100, 1) : 0;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
            <div>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Campaigns</p>
                <p class="text-2xl font-semibold text-slate-900">{{ $data->total() }}</p>
            </div>
            <div class="h-10 w-10 bg-slate-50 rounded-full grid place-content-center text-slate-400 border border-slate-100 group-hover:bg-slate-100 transition-colors">
                <i class="hgi hgi-stroke hgi-megaphone-03 text-lg text-slate-600"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
            <div>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Click Performance</p>
                <p class="text-2xl font-semibold text-slate-900">{{ $avgClickRate }}%</p>
            </div>
            <div class="h-10 w-10 bg-indigo-50 rounded-full grid place-content-center text-slate-400 border border-slate-100 group-hover:bg-indigo-100 transition-colors">
                <i class="hgi hgi-stroke hgi-cursor-pointer-01 text-indigo-600 text-lg"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
            <div>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Avg. Open Rate</p>
                <p class="text-2xl font-semibold text-slate-900">{{ $avgOpenRate }}%</p>
            </div>
            <div class="h-10 w-10 bg-green-50 rounded-full grid place-content-center text-slate-400 border border-slate-100 group-hover:bg-green-100 transition-colors">
                <i class="hgi hgi-stroke hgi-mail-open-01 text-green-600 text-lg"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
            <div>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Unsubscribe Rate</p>
                <p class="text-2xl font-semibold text-slate-900">{{ $avgUnsubRate }}%</p>
            </div>
            <div class="h-10 w-10 bg-red-50 rounded-full grid place-content-center text-slate-400 border border-slate-100 group-hover:bg-amber-100 transition-colors">
                <i class="hgi hgi-stroke hgi-user-block-02 text-lg text-red-600"></i>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-100 shadow-sm mb-6 flex items-center gap-3">
            <i class="hgi hgi-stroke hgi-tick-01"></i>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden text-nowrap">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-slate-200 bg-gradient-to-r from-[#f0f9f9] via-[#e6f2f2] to-[#d1e6e6]">
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-3.5 w-12 text-center"><span class="font-semibold text-slate-500">#</span></th>
                        <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Campaign Identity</th>
                        <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Total receipts</th>
                        <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Schedule</th>
                        <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Performance</th>
                        <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Reach</th>
                        <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($data as $value)
                    <tr class="transition-all duration-200 ease-out hover:bg-slate-50/80">
                        <td class="px-6 py-5 text-center">
                            <div>
                                <i class="hgi hgi-stroke hgi-megaphone-03 text-[#1f8084] text-lg"></i>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <a href="{{route('app.campaign.edit', $value->id)}}" class="text-sm font-semibold text-slate-600 hover:text-teal-600 transition-colors block mb-1 max-w-[280px] truncate" title="{{ $value->name }}">{{ $value->name }}</a>
                            <span class="text-[11px] text-slate-500 font-medium block">Modified {{ $value->updated_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <p class="text-base font-semibold text-slate-600 mb-1">{{ number_format($value->total_recipients) }}</p>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Recipients</p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <p class="text-sm font-semibold text-slate-600 mb-1">{{ $value->updated_at->format('M d, Y') }}</p>
                            <p class="text-[11px] text-slate-500 font-medium">{{ $value->updated_at->format('h:i A') }}</p>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $totalOpened  = 0;
                                $totalClicked = 0;
                                $totalUnsubs  = 0;
                                foreach($value->stats as $stat) {
                                    $totalOpened  += ($stat->opened    != '' ? count(explode(',', $stat->opened))     : 0);
                                    $totalClicked += ($stat->clicked   != '' ? count(explode(',', $stat->clicked))    : 0);
                                    $totalUnsubs  += ($stat->black_list != '' ? count(explode(',', $stat->black_list)) : 0);
                                }
                            @endphp
                            <div class="flex items-center justify-center gap-8">
                                <div class="text-center">
                                    <p class="text-sm font-black text-teal-600 mb-1">{{ number_format($totalOpened) }}</p>
                                    <p class="text-[10px] font-medium text-slate-500 uppercase">Opens</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-black text-indigo-600 mb-1">{{ number_format($totalClicked) }}</p>
                                    <p class="text-[10px] font-medium text-slate-500 uppercase">Clicks</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-black text-red-600 mb-1">{{ number_format($totalUnsubs) }}</p>
                                    <p class="text-[10px] font-medium text-slate-500 uppercase">Unsubs</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @php $totalReach = 0; @endphp
                            @foreach($value->stats as $stat) @php $totalReach += count(explode(',', $stat->total_sent)); @endphp @endforeach
                            <p class="text-base font-semibold text-slate-600 mb-1">{{ number_format($totalReach) }}</p>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Sent</p>
                        </td>
                        <td class="px-4 py-5">
                            <div class="flex justify-end items-center">
                                {{-- Run button now opens confirmation modal --}}
                                <button 
                                    onclick="openRunModal({{ $value->id }}, '{{ addslashes($value->name) }}')" 
                                    class="p-2 hover:bg-slate-100 text-slate-600 hover:text-slate-900 rounded-lg transition-all" 
                                    title="Run">
                                    <i class="hgi hgi-stroke hgi-sent-02 text-lg"></i>
                                </button>
                                <a href="{{route('app.campaign.edit', $value->id)}}" class="p-2 hover:bg-slate-100 text-slate-600 hover:text-slate-900 rounded-lg transition-all" title="Edit">
                                    <i class="hgi hgi-stroke hgi-pencil-edit-02 text-lg"></i>
                                </a>
                                <button onclick="openDeleteModal({{ $value->id }})" class="p-2 hover:bg-red-50 text-slate-600 hover:text-red-500 rounded-lg transition-all" title="Delete">
                                    <i class="hgi hgi-stroke hgi-delete-02 text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-24 text-center">
                            <div class="">
                                <i class="hgi hgi-stroke hgi-mail-01 text-5xl text-slate-300 mb-4 block"></i>
                                <h3 class="text-lg font-bold text-slate-900 mb-1">No campaigns found</h3>
                                <p class="text-sm text-slate-500 mb-6">Launch your first campaign to start seeing engagement analytics.</p>
                                <button onclick="openCreateModal()" class="text-sm font-bold text-teal-600 hover:underline">Get started now &rarr;</button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- --------------------------------------------------
            Run Campaign Confirmation Modal 
------------------------------------------------------>
<div id="runModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-xl text-center">
        <div class="w-14 h-14 mx-auto flex items-center justify-center rounded-full bg-teal-100 mb-4">
            <i class="hgi hgi-stroke hgi-sent-02 text-2xl text-teal-600"></i>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 mb-1">Run Campaign?</h2>
        <p class="text-xs text-slate-500 mb-6">This will immediately dispatch emails to all subscribers in this campaign.</p>

        <form id="runForm" method="POST" action="{{ route('app.campaign.send') }}">
            @csrf
            <input type="hidden" name="id" id="runCampaignId" />

            <div class="flex gap-3">
                <button type="button" onclick="closeRunModal()"
                        class="flex-1 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 py-2 rounded-lg bg-gradient-to-br from-[#007682] to-[#408b86] text-white text-sm font-bold hover:brightness-110 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="hgi hgi-stroke hgi-sent-02"></i>
                    <span>Yes, Send Now</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- -------------------------------------------------
            Delete Confirmation Modal 
----------------------------------------------------->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-xl text-center">
        <div class="w-14 h-14 mx-auto flex items-center justify-center rounded-full bg-red-100 mb-4">
            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z" />
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 mb-2">Delete Campaign?</h2>
        <p class="text-sm text-slate-500 mb-6">This action cannot be undone.</p>
        
        <form id="deleteForm" method="post" action="{{route('app.campaign.delete')}}">
            @csrf
            <input type="hidden" name="id" id="deleteCampaignId" />
            
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()" 
                        class="flex-1 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 py-2 rounded-lg bg-red-600 text-white text-sm font-bold hover:bg-red-700 transition">
                    Delete
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Create Modal
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Run Modal
function openRunModal(campaignId, campaignName) {
    document.getElementById('runCampaignId').value = campaignId;
    document.getElementById('runModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeRunModal() {
    document.getElementById('runModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Delete Modal
function openDeleteModal(campaignId) {
    document.getElementById('deleteCampaignId').value = campaignId;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modals when clicking backdrop
document.getElementById('createModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});
document.getElementById('runModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRunModal();
});
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// ESC key closes all modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateModal();
        closeRunModal();
        closeDeleteModal();
    }
});
</script>

@endsection
