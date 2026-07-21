@extends('marketing.layouts.app')
<title>ACCOUNT : CREATE GROUP</title>

@section('content')
<div class="w-full min-h-screen bg-slate-50/50 p-8 text-slate-900">
    <div class="max-w-7xl mx-auto">
        
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Create Group</h1>
                <p class="text-slate-500 font-medium text-sm">Organize your contacts into targeted segments.</p>
            </div>
            
            <a href="{{route('app.group.index')}}" class="inline-flex items-center justify-center rounded-xl text-sm font-bold border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 h-11 px-6 transition-all active:scale-95 shadow-sm">
                <i class="hgi hgi-stroke hgi-arrow-left-02 mr-2"></i>
                Back To Group List
            </a>
        </div>

        {{-- Limit Alert --}}
        @if($rem_groups <= 0)
            <div class="mb-8 p-5 bg-amber-50 border border-amber-100 rounded-2xl flex items-start gap-4 shadow-sm cursor-pointer hover:bg-amber-100/50 transition" onclick="openUpgradeModal()">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-amber-200">
                    <i class="hgi hgi-stroke hgi-crown text-white text-lg"></i>
                </div>
                <div>
                    <h4 class="text-[15px] font-black text-amber-800 leading-tight tracking-tight">Upgrade to Create More Groups</h4>
                    <p class="text-[13px] text-amber-600 font-medium mt-1 leading-relaxed">
                        You've reached your group limit. Click here to view available plans and upgrade.
                    </p>
                </div>
            </div>
        @else
            {{-- Visual Placeholder while modal is open --}}
            <div class="p-20 border-2 border-dashed border-slate-200 rounded-[2.5rem] flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                    <i class="hgi hgi-stroke hgi-user-group text-2xl text-slate-400"></i>
                </div>
                <button onclick="openCreateGroupModal()" class="text-teal-600 font-black uppercase tracking-widest text-xs hover:underline">
                    Re-open creation form
                </button>
            </div>
        @endif

    </div>
</div>

{{-- Create Group Modal --}}
<div id="createGroupModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/20 backdrop-blur-sm p-4">
    <div class="bg-gradient-to-br from-blue-50 via-blue-50/50 to-teal-50/30 w-full max-w-lg rounded-xl shadow-2xl overflow-hidden relative">
        <div class="relative bg-white backdrop-blur-xl rounded-2xl shadow-lg border">
            <div class="text-center pt-14 pb-12 px-8 bg-gradient-to-b from-[#007682]/20 via-[#408b86]/10 to-white">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <i class="hgi hgi-stroke hgi-user-group text-2xl text-teal-600"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mb-2">Create New Group</h2>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Organize your contacts into targeted segments for more effective campaigns.
                </p>
            </div>
            
            <form method="post" action="{{ route('app.group.store') }}" class="px-8 pb-8" onsubmit="return handleCreateGroupSubmit(event)">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Group Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" maxlength="100" required
                        placeholder="e.g. Newsletter Subscribers, VIP Customers"
                        class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-white focus:border-teal-400 focus:ring-4 focus:ring-teal-500/10 outline-none transition text-slate-700 placeholder:text-slate-400" />
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description <span class="text-slate-400 font-normal">(Optional)</span></label>
                    <textarea name="description" rows="3" maxlength="500"
                        placeholder="Add details about this group's purpose..."
                        class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-white focus:border-teal-400 focus:ring-4 focus:ring-teal-500/10 outline-none transition text-slate-700 placeholder:text-slate-400 resize-none"></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeCreateGroupModal()"
                        class="flex-1 py-3 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-gradient-to-br from-[#007682] to-[#408b86] hover:brightness-110 flex-1 py-3 rounded-xl text-white text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="hgi hgi-stroke hgi-user-add-01"></i>
                        <span>Create Group</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Auto-open modal on page visit
    window.onload = function() {
        @if($rem_groups > 0)
            openCreateGroupModal();
        @endif
    };

    function openCreateGroupModal() {
        document.getElementById('createGroupModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateGroupModal() {
        document.getElementById('createGroupModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Backdrop click close
    document.getElementById('createGroupModal').addEventListener('click', function(e) {
        if (e.target === this) closeCreateGroupModal();
    });

    // ESC key close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeCreateGroupModal();
    });
</script>
@endsection
