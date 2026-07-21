@extends('marketing.layouts.app')

@section('content')
<div class="w-full min-h-screen bg-slate-50/50 p-8 text-slate-900">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Contact Groups</h1>
                <p class="text-slate-500 font-medium">Organize and manage your audience segments effectively.</p>
            </div>
            
            <button onclick="openCreateGroupModal()" 
                class="flex items-center gap-3 px-6 py-2 rounded-lg text-white 
                    bg-gradient-to-br from-[#007682] to-[#408b86]
                    hover:brightness-110
                    transition-all duration-300 active:scale-95
                    shadow-lg">
                <i class="hgi hgi-stroke hgi-user-multiple-02 text-lg"></i>
                <span class="font-bold text-sm">Create New Group</span>
            </button>
        </div>

        {{-- 
            $totalContacts and $totalUnsubscribe come from the controller.
            We only compute $totalWithConts here for any local use.
        --}}
        @php
            $totalWithConts = 0;
            foreach ($data as $g) {
                $cnt = $g->count ?? 0;
                if ($cnt > 0) $totalWithConts++;
            }
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

            {{-- Total Groups --}}
            <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
                <div>
                    <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Groups</p>
                    <p class="text-2xl font-semibold text-slate-900">{{ $data->total() }}</p>
                </div>
                <div class="h-10 w-10 bg-slate-50 rounded-full grid place-content-center border border-slate-100 group-hover:bg-slate-100 transition-colors">
                    <i class="hgi hgi-stroke hgi-user-multiple-02 text-lg text-slate-600"></i>
                </div>
            </div>

            {{-- Total Contacts --}}
            <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
                <div>
                    <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Contacts</p>
                    <p class="text-2xl font-semibold text-slate-900">{{ $totalContacts }}</p>
                </div>
                <div class="h-10 w-10 bg-teal-50 rounded-full grid place-content-center border border-slate-100 group-hover:bg-teal-100 transition-colors">
                    <i class="hgi hgi-stroke hgi-user-group-02 text-lg text-teal-600"></i>
                </div>
            </div>

            {{-- Total Subscribers (contacts minus unsubscribers) --}}
            <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
                <div>
                    <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Subscribers</p>
                    <p class="text-2xl font-semibold text-slate-900">{{ $totalContacts - $totalUnsubscribe }}</p>
                </div>
                <div class="h-10 w-10 bg-indigo-50 rounded-full grid place-content-center border border-slate-100 group-hover:bg-indigo-100 transition-colors">
                    <i class="hgi hgi-stroke hgi-checkmark-circle-02 text-lg text-indigo-600"></i>
                </div>
            </div>

            {{-- Total Unsubscribers --}}
            <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
                <div>
                    <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Unsubscribers</p>
                    <p class="text-2xl font-semibold text-slate-900">{{ $totalUnsubscribe }}</p>
                </div>
                <div class="h-10 w-10 bg-red-50 rounded-full grid place-content-center border border-slate-100 group-hover:bg-amber-100 transition-colors">
                    <i class="hgi hgi-stroke hgi-user-block-02 text-lg text-red-500"></i>
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
                            <th class="px-6 py-3.5 w-12 text-start">
                                <span class="font-semibold text-slate-500">#</span>
                            </th>
                            <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                Group Identity
                            </th>
                            <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">
                                Audience Size
                            </th>
                            <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">
                                Acquisition Source
                            </th>
                            <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">
                                Date Created
                            </th>
                            <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($data as $value)
                        <tr class="transition-all duration-200 ease-out hover:bg-slate-50/80">
                            <td class="px-6 py-3 text-center">
                                <i class="hgi hgi-stroke hgi-user-multiple-02 text-[#1f8084] text-xl"></i>
                            </td>

                            <td class="px-6 py-3">
                                <div class="text-sm font-bold text-slate-600 mb-1">
                                    {{ $value->name }}
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($value->description)
                                        <span class="text-[11px] text-slate-500 font-medium block">
                                            {{ Str::limit($value->description, 30) }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-3 text-center">
                                <p class="text-base font-black text-slate-600">
                                    {{ $value->count ?? 0 }}
                                </p>
                                <p class="text-[11px] font-medium text-slate-500">
                                    Contacts
                                </p>
                            </td>

                            <td class="px-6 py-3 text-center">
                                <span class="bg-teal-700 text-white text-[10px] font-semibold px-2 py-1 rounded uppercase tracking-wider">
                                    Manual Import
                                </span>
                            </td>

                            <td class="px-6 py-3 text-center">
                                <p class="text-sm font-bold text-slate-600 mb-1">
                                    {{ $value->created_at->format('M d, Y') }}
                                </p>
                                <p class="text-[11px] font-medium text-slate-500 uppercase">
                                    {{ $value->created_at->format('h:i A') }}
                                </p>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex justify-end items-center">
                                    <a href="{{ route('app.contact.index', $value->id) }}"
                                        class="p-2 hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded-lg transition-all"
                                        title="Manage Contacts">
                                        <i class="hgi hgi-stroke hgi-user-multiple-02 text-lg"></i>
                                    </a>

                                    <button onclick="openEditModal({{ $value->id }}, '{{ addslashes($value->name) }}', '{{ addslashes($value->description ?? '') }}')"
                                        class="p-2 hover:bg-slate-100 text-slate-600 hover:text-slate-900 rounded-lg transition-all"
                                        title="Edit">
                                        <i class="hgi hgi-stroke hgi-pencil-edit-02 text-lg"></i>
                                    </button>

                                    <button onclick="openDeleteModal({{ $value->id }})"
                                        class="p-2 hover:bg-red-50 text-slate-600 hover:text-red-500 rounded-lg transition-all"
                                        title="Delete">
                                        <i class="hgi hgi-stroke hgi-delete-02 text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-24 text-center">
                                <div class="mx-auto">
                                    <i class="hgi hgi-stroke hgi-user-group text-5xl text-slate-300 mb-4 block"></i>
                                    <h3 class="text-lg font-bold text-slate-900 mb-1">No groups found</h3>
                                    <p class="text-sm text-slate-500 mb-6">
                                        Create a group to start organizing your contacts for campaigns.
                                    </p>
                                    <button onclick="openCreateGroupModal()"
                                        class="text-sm font-bold text-teal-600 hover:underline">
                                        Create your first group →
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-end mt-4">
            <nav aria-label="Page navigation">
                <ul class="flex -space-x-px text-sm">
                    @if ($data->onFirstPage())
                        <li><span class="flex items-center justify-center text-slate-300 bg-slate-50 border border-slate-200 cursor-not-allowed rounded-s-lg px-3 h-10">Previous</span></li>
                    @else
                        <li><a href="{{ $data->previousPageUrl() }}" class="flex items-center justify-center text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 font-medium rounded-s-lg px-3 h-10 transition-colors">Previous</a></li>
                    @endif

                    @foreach ($data->getUrlRange(max(1, $data->currentPage() - 2), min($data->lastPage(), $data->currentPage() + 2)) as $page => $url)
                        <li>
                            @if ($page == $data->currentPage())
                                <span aria-current="page" class="flex items-center justify-center text-teal-600 bg-teal-50 border border-teal-200 font-bold w-10 h-10">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="flex items-center justify-center text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 font-medium w-10 h-10 transition-colors">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach

                    @if ($data->hasMorePages())
                        <li><a href="{{ $data->nextPageUrl() }}" class="flex items-center justify-center text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 font-medium rounded-e-lg px-3 h-10 transition-colors">Next</a></li>
                    @else
                        <li><span class="flex items-center justify-center text-slate-300 bg-slate-50 border border-slate-200 cursor-not-allowed rounded-e-lg px-3 h-10">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Create Group Modal -->
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
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Group Name</label>
                    <input type="text" name="name" maxlength="100" required
                        placeholder="e.g. Newsletter Subscribers, VIP Customers"
                        class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-white focus:border-teal-400 focus:ring-4 focus:ring-teal-500/10 outline-none transition text-slate-700 placeholder:text-slate-400" />
                    <p class="mt-2 text-xs text-slate-500">Choose a descriptive name to easily identify this audience segment.</p>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description <span class="text-slate-400 font-normal">(Optional)</span></label>
                    <textarea name="description" rows="3" maxlength="500"
                        placeholder="Add details about this group's purpose or criteria..."
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

<!-- Edit Group Modal -->
<div id="editGroupModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/20 backdrop-blur-sm p-4">
    <div class="bg-gradient-to-br from-blue-50 via-blue-50/50 to-teal-50/30 w-full max-w-lg rounded-xl shadow-2xl overflow-hidden relative">
        <div class="relative bg-white backdrop-blur-xl rounded-2xl shadow-lg border">
            <div class="text-center pt-14 pb-12 px-8 bg-gradient-to-b from-[#007682]/20 via-[#408b86]/10 to-white">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <i class="hgi hgi-stroke hgi-pencil-edit-02 text-2xl text-teal-600"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-900 mb-2">Edit Group</h2>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Update your group details and organize your audience better.
                </p>
            </div>
            <form method="post" action="{{ route('app.group.update') }}" class="px-8 pb-8">
                @csrf
                <input type="hidden" name="id" id="editGroupId" />
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Group Name</label>
                    <input type="text" name="name" id="editGroupName" maxlength="100" required
                        placeholder="e.g. Newsletter Subscribers, VIP Customers"
                        class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-white focus:border-teal-400 focus:ring-4 focus:ring-teal-500/10 outline-none transition text-slate-700 placeholder:text-slate-400" />
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description <span class="text-slate-400 font-normal">(Optional)</span></label>
                    <textarea name="description" id="editGroupDescription" rows="3" maxlength="500"
                        placeholder="Add details about this group's purpose or criteria..."
                        class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-white focus:border-teal-400 focus:ring-4 focus:ring-teal-500/10 outline-none transition text-slate-700 placeholder:text-slate-400 resize-none"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeEditModal()"
                        class="flex-1 py-3 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-gradient-to-br from-[#007682] to-[#408b86] hover:brightness-110 flex-1 py-3 rounded-xl text-white text-sm font-bold transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="hgi hgi-stroke hgi-checkmark-circle-02"></i>
                        <span>Update Group</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-xl text-center">
        <div class="w-14 h-14 mx-auto flex items-center justify-center rounded-full bg-red-100 mb-4">
            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z" />
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 mb-2">Delete Group?</h2>
        <p class="text-sm text-slate-500 mb-6">This will permanently delete this group and all its contacts. This action cannot be undone.</p>
        <form id="deleteForm" method="post" action="{{ route('app.group.delete') }}">
            @csrf
            <input type="hidden" name="id" id="deleteGroupId" />
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
function openCreateGroupModal() {
    document.getElementById('createGroupModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeCreateGroupModal() {
    document.getElementById('createGroupModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openEditModal(groupId, groupName, groupDescription) {
    document.getElementById('editGroupId').value = groupId;
    document.getElementById('editGroupName').value = groupName;
    document.getElementById('editGroupDescription').value = groupDescription;
    document.getElementById('editGroupModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editGroupModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openDeleteModal(groupId) {
    document.getElementById('deleteGroupId').value = groupId;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

['createGroupModal', 'editGroupModal', 'deleteModal'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) this.id === 'createGroupModal' ? closeCreateGroupModal()
            : this.id === 'editGroupModal' ? closeEditModal()
            : closeDeleteModal();
    });
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateGroupModal();
        closeEditModal();
        closeDeleteModal();
    }
});
</script>

@endsection
