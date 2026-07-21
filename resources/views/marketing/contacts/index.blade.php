@extends('marketing.layouts.app')

<title>ACCOUNT : GROUP CONTACTS</title>

@section('content')
<div class="w-full min-h-full bg-slate-50/50 px-8 py-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Group Contacts</h1>
            <p class="text-slate-500 font-medium">Manage subscribers, opt-in status, and communication channels.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <button onclick="openImportModal()" 
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all shadow-sm">
                <i class="hgi hgi-stroke hgi-file-import text-lg"></i>
                Import
            </button>
            <button onclick="openContactModal()"
                class="flex items-center gap-3 px-6 py-2 rounded-lg text-white 
                    bg-gradient-to-br from-[#007682] to-[#408b86]
                    hover:brightness-110
                    transition-all duration-300 active:scale-95
                    shadow-lg">
                <i class="hgi hgi-stroke hgi-user-add-01 text-lg"></i>
                <span class="font-bold text-sm">Add Contact</span>
            </button>
        </div>
    </div>


    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-100 shadow-sm mb-6 flex items-center gap-3">
            <i class="hgi hgi-stroke hgi-tick-01"></i>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-[#FFF1F3] border border-[#FFE4E8] rounded-xl shadow-sm flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="text-[#C12E4E]">
                    <i class="hgi hgi-stroke hgi-sad-01 text-xl"></i>
                </div>
                <div>
                    <p class="text-[14px] font-semibold text-[#C12E4E] leading-tight">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
            <a href="#" class="text-[13px] font-bold text-[#C12E4E] underline decoration-2 underline-offset-4 hover:text-[#9b253e] transition-colors">
                Send crash report
            </a>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table id="contactTable" class="w-full text-left border-collapse">
                <thead class="border-b border-slate-200 bg-gradient-to-r from-[#f0f9f9] via-[#e6f2f2] to-[#d1e6e6]">
                    <tr>
                        {{-- Header select-all cell --}}
                        <th class="px-6 py-3.5 w-12 text-center cursor-pointer select-none" id="selectAllTh" onclick="toggleSelectAll()">
                            <div class="relative w-6 h-6 flex items-center justify-center mx-auto">
                                {{-- Hash label (default) --}}
                                <span id="hashLabel" class="font-semibold text-slate-500 text-sm">#</span>
                                {{-- Fake checkbox visual (shown on hover / when active) --}}
                                <span id="selectAllBox"
                                    class="hidden w-4 h-4 rounded border-2 border-slate-400 bg-white items-center justify-center cursor-pointer">
                                    <svg id="selectAllTick" class="hidden w-3 h-3 text-teal-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span id="selectAllDash" class="hidden w-2 h-0.5 bg-teal-600 rounded"></span>
                                </span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Identity</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Timeline</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">OPT</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Channels</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($data as $key => $value)
                    <tr class="contact-row transition-all hover:bg-slate-50/80 group">

                        {{-- Hover → checkbox cell --}}
                        <td class="px-6 py-3 contact-check-cell" data-id="{{ $value->id }}">
                            <div class="relative w-6 h-6 flex items-center justify-center mx-auto">
                                @if($value->exist == 0)
                                    <div class="contact-icon w-6 h-6 bg-red-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-black text-red-500">!</span>
                                    </div>
                                @else
                                    <i class="contact-icon hgi hgi-stroke hgi-call-02 text-[#1f8084] text-lg transition-all"></i>
                                @endif
                                <input type="checkbox"
                                    class="contact-checkbox hidden absolute inset-0 w-4 h-4 m-auto accent-teal-600 cursor-pointer rounded"
                                    value="{{ $value->id }}">
                            </div>
                        </td>

                        <td class="px-6 py-3">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-slate-600 hover:text-teal-600 transition-colors block mb-1 max-w-[350px] truncate">
                                    {{ $value->firstname }} {{ $value->lastname }}
                                </span>
                                <span class="text-[12px] text-slate-500 font-medium block">{{ $value->email }}</span>
                                @if($value->exist == 0)
                                    <span class="text-[11px] text-red-500 font-medium">Not Working</span>
                                @endif
                            </div>
                        </td>

                        <td class="px-6 py-3 text-start">
                            <p class="text-sm font-semibold text-slate-600 mb-1">{{ $value->created_at->format('d M, Y') }}</p>
                            <p class="text-[12px] text-slate-500 font-medium block">
                                {{ $value->created_at->format('h:i A') }}
                            </p>
                        </td>

                        <td class="text-center">
                            @if($value->is_unsubscribed)
                                <span class="px-2 py-1 text-[10px] font-semibold bg-red-800 rounded-lg text-white">
                                    Unsubscribed
                                </span>
                            @else
                                <span class="px-2 py-1 text-[10px] font-semibold bg-[#1c7f84] rounded-lg text-white">
                                    Subscribed
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-3">
                            <div class="flex flex-col items-center gap-1">
                                <span class="text-sm font-black {{ $value->opt_in == 'Yes' ? 'text-teal-600' : 'text-slate-500' }}">{{ $value->opt_in }}</span>
                                <span class="text-[10px] font-bold uppercase text-slate-500">Opt-in</span>
                            </div>
                        </td>

                        <td class="px-6 py-3">
                            <div class="flex items-center justify-center gap-2">

                                {{-- SMS --}}
                                <div class="relative inline-block">
                                    <span class="peer px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter cursor-pointer
                                        {{ $value->sms ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-500' }}">
                                        SMS
                                    </span>
                                    @if($value->sms)
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2
                                            opacity-0 invisible peer-hover:opacity-100 peer-hover:visible
                                            transition-all duration-200
                                            bg-slate-900 text-white text-[10px] px-2 py-1 rounded shadow-lg whitespace-nowrap z-50 pointer-events-none">
                                            {{ $value->sms }}
                                        </div>
                                    @endif
                                </div>

                                {{-- WhatsApp --}}
                                <div class="relative inline-block">
                                    <span class="peer px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter cursor-pointer
                                        {{ $value->whatsapp ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-500' }}">
                                        WA
                                    </span>
                                    @if($value->whatsapp)
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2
                                            opacity-0 invisible peer-hover:opacity-100 peer-hover:visible
                                            transition-all duration-200
                                            bg-slate-900 text-white text-[10px] px-2 py-1 rounded shadow-lg whitespace-nowrap z-50 pointer-events-none">
                                            {{ $value->whatsapp }}
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </td>

                        <td class="px-6 py-3">
                            <div class="flex justify-end items-center gap-1">
                                <button onclick="openEditModal({{ json_encode($value) }})"
                                    class="p-2 hover:bg-slate-100 text-slate-600 hover:text-slate-900 rounded-lg transition-all" title="Edit">
                                    <i class="hgi hgi-stroke hgi-pencil-edit-02 text-lg"></i>
                                </button>
                                <button onclick="openDeleteModal({{ $value->id }})"
                                    class="p-2 hover:bg-red-100 text-slate-600 hover:text-red-900 rounded-lg transition-all" title="Delete">
                                    <i class="hgi hgi-stroke hgi-delete-02 text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <i class="hgi hgi-stroke hgi-user-multiple-02 text-5xl text-slate-400 mb-4"></i>
                                <h3 class="text-lg font-bold text-slate-800">No contacts here</h3>
                                <p class="text-sm text-slate-500 mb-6">Start by importing or adding a manual contact.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($data->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-white">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">

                {{-- Left Info --}}
                <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
                    <span>Showing</span>
                    <span class="px-2 py-1 rounded-md text-slate-800 font-bold">{{ $data->firstItem() }}</span>
                    <span>to</span>
                    <span class="px-2 py-1 rounded-md text-slate-800 font-bold">{{ $data->lastItem() }}</span>
                    <span>of</span>
                    <span class="px-2 py-1 rounded-md text-teal-700 font-black">{{ $data->total() }}</span>
                    <span class="text-slate-400">contacts</span>
                </div>

                {{-- Pagination --}}
                <div class="flex items-center gap-1">

                    {{-- Previous --}}
                    @if ($data->onFirstPage())
                        <span class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed">Prev</span>
                    @else
                        <a href="{{ $data->previousPageUrl() }}"
                            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">Prev</a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($data->onEachSide(1)->links()->elements as $element)
                        @if (is_string($element))
                            <span class="px-2 text-slate-400 text-xs font-bold">{{ $element }}</span>
                        @endif
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $data->currentPage())
                                    <span class="px-3 py-1.5 text-xs font-black rounded-lg text-white bg-gradient-to-br from-[#007682] to-[#408b86] shadow-md">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="px-3 py-1.5 text-xs font-bold rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($data->hasMorePages())
                        <a href="{{ $data->nextPageUrl() }}"
                            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">Next</a>
                    @else
                        <span class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed">Next</span>
                    @endif

                </div>
            </div>
        </div>
        @endif
    </div>
</div>


{{-- ── Floating Bulk Action Bar ─────────────────────────────────────────── --}}
<div id="bulkActionBar"
    class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-[80]
           bg-white border border-slate-200 rounded-full shadow-lg 
           px-2 py-2 flex items-center gap-3
           transition-all duration-300 ease-in-out">
    
    <div class="flex items-center gap-2 pl-3 pr-1">
        <span class="flex h-2 w-2 rounded-full bg-[#208184]"></span>
        <span class="text-xs font-semibold text-slate-700 whitespace-nowrap">
            <span id="selectedCount">0</span> Selected
        </span>
    </div>

    <div class="w-px h-4 bg-slate-200"></div>

    <div class="flex items-center gap-1.5 pr-1">
        <button onclick="openBulkDeleteModal()"
            class="flex items-center border gap-1.5 px-3 py-1.5 rounded-full hover:bg-red-50 text-red-600 text-xs font-bold transition-colors">
            <i class="hgi hgi-stroke hgi-delete-02 text-sm"></i>
            Delete
        </button>
        
        <button onclick="clearSelection()"
            class="flex items-center border justify-center w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 text-slate-500 transition-all">
            <i class="hgi hgi-stroke hgi-cancel-01 text-sm"></i>
        </button>
    </div>
</div>

{{-- ── Bulk Delete Confirm Modal ────────────────────────────────────────── --}}
<div id="bulkDeleteModal" class="hidden fixed inset-0 z-[90] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-xl text-center">
        <div class="w-14 h-14 mx-auto flex items-center justify-center rounded-full bg-red-100 mb-4">
            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z" />
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 mb-2">Delete Selected Contacts?</h2>
        <p class="text-sm text-slate-500 mb-1">You are about to delete</p>
        <p class="text-lg font-black text-red-500 mb-1"><span id="bulkDeleteCount">0</span> contact(s)</p>
        <p class="text-sm text-slate-500 mb-6">This action cannot be undone.</p>
        <div class="flex gap-3">
            <button type="button" onclick="closeBulkDeleteModal()"
                class="flex-1 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition">
                Cancel
            </button>
            <button type="button" onclick="executeBulkDelete()"
                class="flex-1 py-2 rounded-lg bg-red-600 text-white text-sm font-bold hover:bg-red-700 transition flex items-center justify-center gap-2">
                <span id="bulkDeleteBtnText">Yes, Delete</span>
                <svg id="bulkDeleteSpinner" class="hidden animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>


{{-- ── Single Delete Modal ──────────────────────────────────────────────── --}}
<div id="deleteModal" class="hidden fixed inset-0 z-[70] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-xl text-center">
        <div class="w-14 h-14 mx-auto flex items-center justify-center rounded-full bg-red-100 mb-4">
            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z" />
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 mb-2">Delete Contact?</h2>
        <p class="text-sm text-slate-500 mb-6">This action cannot be undone.</p>
        <form id="deleteForm" method="post" action="{{ route('app.contact.delete') }}">
            @csrf
            <input type="hidden" name="id" id="deleteContactId" />
            <input type="hidden" name="group_id" value="{{ $groupId }}" />
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


{{-- ── Edit Contact Modal ───────────────────────────────────────────────── --}}
<div id="editContactModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
    <div class="w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden relative animate-in fade-in zoom-in-95 duration-200">
        <div class="relative bg-white rounded-2xl shadow-lg border border-slate-100">
            <div class="text-center pt-10 pb-6 px-8 bg-gradient-to-b from-[#007682]/10 via-[#408b86]/5 to-white">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-teal-50">
                    <i class="hgi hgi-stroke hgi-pencil-edit-02 text-2xl text-teal-600"></i>
                </div>
                <h2 class="text-xl font-black text-slate-900 mb-1">Edit Contact</h2>
                <p class="text-sm text-slate-500">Update subscriber details and preferences.</p>
            </div>
            <form method="POST" action="{{ route('app.contact.update') }}" class="px-6 pb-8 mb-0">
                @csrf
                <input type="hidden" name="id" id="edit-id">
                <input type="hidden" name="group_id" id="edit-group-id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-2 ml-1">Email Address</label>
                        <div class="relative">
                            <i class="hgi hgi-stroke hgi-mail-01 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="email" name="email" id="edit-email" required
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-teal-400 focus:ring-4 focus:ring-teal-500/10 focus:bg-white outline-none transition font-medium text-slate-700">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="firstname" id="edit-firstname" placeholder="First Name"
                            class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-teal-400 focus:bg-white transition text-sm">
                        <input type="text" name="lastname" id="edit-lastname" placeholder="Last Name"
                            class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-teal-400 focus:bg-white transition text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-1.5 ml-1">SMS</label>
                            <input type="text" name="sms" id="edit-sms" placeholder="+1..."
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-teal-400 outline-none text-sm transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-1.5 ml-1">WhatsApp</label>
                            <input type="text" name="whatsapp" id="edit-whatsapp" placeholder="+1..."
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-teal-400 outline-none text-sm transition">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-1.5 ml-1">Opt-In</label>
                            <select name="opt_in" id="edit-opt-in"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-teal-400 outline-none text-sm transition">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-1.5 ml-1">Double Opt-In</label>
                            <select name="double_opt_in" id="edit-double-opt-in"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-teal-400 outline-none text-sm transition">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="closeEditModal()"
                        class="flex-1 py-3.5 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold hover:bg-slate-200 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-gradient-to-br from-[#007682] to-[#408b86] py-3.5 rounded-xl text-white text-sm font-bold shadow-lg shadow-teal-900/20 hover:brightness-110 transition-all">
                        Update Contact
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ── Import Modal ─────────────────────────────────────────────────────── --}}
<div id="importModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
    <div class="w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden relative animate-in fade-in zoom-in-95 duration-200">
        <div class="relative bg-white rounded-2xl shadow-lg border border-slate-100">
            <button onclick="closeImportModal()" class="absolute top-4 right-4 z-10 p-2 text-slate-400 hover:text-red-500 rounded-full transition-all">
                <i class="hgi hgi-stroke hgi-cancel-01 text-xl"></i>
            </button>
            <div class="text-center pt-10 pb-6 px-8 bg-gradient-to-b from-[#007682]/10 via-[#408b86]/5 to-white">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-teal-50">
                    <i class="hgi hgi-stroke hgi-file-import text-2xl text-teal-600"></i>
                </div>
                <h2 class="text-xl font-black text-slate-900 mb-1">Import Contacts</h2>
                <p class="text-sm text-slate-500 leading-relaxed">Bulk upload your subscribers from a file.</p>
            </div>
            <div class="px-8 mb-6 flex justify-center">
                <div class="flex p-1 bg-slate-100/80 rounded-lg border border-slate-200 w-full max-w-[280px]">
                    <button onclick="switchTab('hybrid')" id="tab-hybrid"
                        class="flex-1 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-md transition-all text-teal-700 bg-white shadow-sm border border-slate-200/50">
                        Hybridmail
                    </button>
                    <button onclick="switchTab('google')" id="tab-google"
                        class="flex-1 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-md transition-all text-slate-500 hover:text-slate-700">
                        Google
                    </button>
                </div>
            </div>
            <div class="px-6 pb-8">
                <div id="content-hybrid" class="space-y-6">
                    <div class="flex items-center justify-between px-1">
                        <label class="block text-[13px] font-semibold text-slate-400">Upload your file</label>
                        <div class="flex items-center gap-2 text-xs font-bold">
                            <a href="{{ asset('public/assets/templates/contact/samplecontact.csv') }}" download
                                class="text-teal-600 hover:text-teal-700 hover:underline transition-all">Download CSV Template</a>
                            <span class="text-slate-300">/</span>
                            <a href="{{ asset('public/assets/templates/contact/samplecontact.txt') }}" download
                                class="text-teal-600 hover:text-teal-700 hover:underline transition-all">TXT Template</a>
                        </div>
                    </div>
                    <form id="import_form_hybrid" method="post" action="{{ route('app.contact.fileimport', $groupId) }}" enctype="multipart/form-data">
                        @csrf
                        <input value="hybrid" name="type" hidden />
                        <input type="file" name="file" id="file-upload-input-hybrid" accept=".csv, .txt" class="hidden">
                        <div id="file-upload-select-hybrid"
                            class="group cursor-pointer border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center hover:border-teal-300 hover:bg-teal-50/30 transition-all">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 group-hover:bg-teal-100 transition-all">
                                <i class="hgi hgi-stroke hgi-cloud-upload text-xl text-slate-400 group-hover:text-teal-600"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-700" id="file-select-name-hybrid">Click or drag file here</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mt-1">CSV or TXT • Max 10MB</p>
                        </div>
                    </form>
                </div>
                <div id="content-google" class="hidden space-y-6">
                    <div class="flex items-center justify-between px-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Upload File</label>
                        <a href="{{ asset('public/assets/templates/contact/samplegoogle.csv') }}" download
                            class="flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 transition-all">
                            Download Google CSV Template
                        </a>
                    </div>
                    <form id="import_form_google" method="post" action="{{ route('app.contact.fileimport', $groupId) }}" enctype="multipart/form-data">
                        @csrf
                        <input value="google" name="type" hidden />
                        <input type="file" name="file" id="file-upload-input-google" accept=".csv" class="hidden">
                        <div id="file-upload-select-google"
                            class="group cursor-pointer border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center hover:border-blue-300 hover:bg-blue-50/30 transition-all">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 group-hover:bg-blue-100 transition-all">
                                <i class="hgi hgi-stroke hgi-cloud-upload text-xl text-slate-400 group-hover:text-blue-600"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-700" id="file-select-name-google">Click or drag file here</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mt-1">Google CSV • Max 10MB</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ── Add Contact Modal ────────────────────────────────────────────────── --}}
<div id="contactModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
    <div class="w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden relative animate-in fade-in zoom-in-95 duration-200">
        <div class="relative bg-white rounded-2xl shadow-lg border border-slate-100">
            <div class="text-center pt-10 pb-6 px-8 bg-gradient-to-b from-[#007682]/10 via-[#408b86]/5 to-white">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-teal-50">
                    <i class="hgi hgi-stroke hgi-user-add-01 text-2xl text-teal-600"></i>
                </div>
                <h2 class="text-xl font-black text-slate-900 mb-1">Add New Contact</h2>
                <p class="text-sm text-slate-500 leading-relaxed px-4">Fill in the details to add a subscriber to this group.</p>
            </div>
            <form method="POST" action="{{ route('app.contact.store') }}" class="px-6 pb-8 mb-0" onsubmit="return handleCreateContactSubmit(event)">
                @csrf
                <input name="groupId" value="{{ $groupId }}" hidden>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-2 ml-1">Email Address</label>
                        <div class="relative">
                            <i class="hgi hgi-stroke hgi-mail-01 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="email" name="email" id="modal-email" required placeholder="alex@example.com"
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-teal-400 focus:ring-4 focus:ring-teal-500/10 focus:bg-white outline-none transition font-medium text-slate-700">
                        </div>
                        <p id="modal-email-error" class="text-red-500 text-[10px] mt-1 font-bold uppercase ml-1"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="firstname" placeholder="First Name"
                            class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-teal-400 focus:bg-white transition text-sm">
                        <input type="text" name="lastname" placeholder="Last Name"
                            class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 outline-none focus:border-teal-400 focus:bg-white transition text-sm">
                    </div>
                    <div>
                        <button type="button" onclick="toggleAdvancedFields()" class="group flex items-center gap-2 text-xs font-bold text-teal-600 hover:text-teal-700 transition-all">
                            <span id="toggleText">Show more details</span>
                            <i id="toggleIcon" class="hgi hgi-stroke hgi-arrow-down-01 transition-transform duration-300"></i>
                        </button>
                        <div id="advancedFields" class="hidden mt-4 space-y-4 animate-in slide-in-from-top-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1.5 ml-1">SMS</label>
                                    <input type="text" name="sms" placeholder="+1..."
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-teal-400 outline-none text-sm transition">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1.5 ml-1">WhatsApp</label>
                                    <input type="text" name="whatsapp" placeholder="+1..."
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-teal-400 outline-none text-sm transition">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1.5 ml-1">Opt-In</label>
                                    <select name="opt_in"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-teal-400 outline-none text-sm transition">
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1.5 ml-1">Double Opt-In</label>
                                    <select name="double_opt_in"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-teal-400 outline-none text-sm transition">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="closeContactModal()"
                        class="flex-1 py-3.5 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold hover:bg-slate-200 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-gradient-to-br from-[#007682] to-[#408b86] py-3.5 rounded-xl text-white text-sm font-bold shadow-lg shadow-teal-900/20 hover:brightness-110 transition-all">
                        Save Contact
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
// ── Multi-Select & Bulk Delete ─────────────────────────────────────────────

let selectedIds  = new Set();
let allSelected  = false;   // tracks the true select-all state

document.addEventListener('DOMContentLoaded', function () {

    // ── Row hover → show checkbox ──────────────────────────────────────────
    document.querySelectorAll('.contact-row').forEach(row => {
        const cell     = row.querySelector('.contact-check-cell');
        if (!cell) return;
        const icon     = cell.querySelector('.contact-icon');
        const checkbox = cell.querySelector('.contact-checkbox');

        row.addEventListener('mouseenter', () => {
            if (!checkbox.checked) {
                icon.classList.add('hidden');
                checkbox.classList.remove('hidden');
            }
        });
        row.addEventListener('mouseleave', () => {
            if (!checkbox.checked) {
                checkbox.classList.add('hidden');
                icon.classList.remove('hidden');
            }
        });

        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                selectedIds.add(checkbox.value);
                row.classList.add('bg-teal-50/60');
                icon.classList.add('hidden');
                checkbox.classList.remove('hidden');
            } else {
                selectedIds.delete(checkbox.value);
                row.classList.remove('bg-teal-50/60');
                checkbox.classList.add('hidden');
                icon.classList.remove('hidden');
                allSelected = false;
            }
            renderSelectAllBox();
            updateBulkBar();
        });
    });

    // ── Header th: show fake-checkbox on hover ─────────────────────────────
    const th = document.getElementById('selectAllTh');
    if (th) {
        th.addEventListener('mouseenter', () => {
            document.getElementById('hashLabel').classList.add('hidden');
            const box = document.getElementById('selectAllBox');
            box.classList.remove('hidden');
            box.classList.add('flex');
        });
        th.addEventListener('mouseleave', () => {
            // Keep checkbox visible if something is selected
            if (selectedIds.size === 0) {
                document.getElementById('hashLabel').classList.remove('hidden');
                const box = document.getElementById('selectAllBox');
                box.classList.add('hidden');
                box.classList.remove('flex');
            }
        });
    }
});

// Called by onclick on the <th>
function toggleSelectAll() {
    const all = document.querySelectorAll('.contact-checkbox');
    allSelected = !allSelected;   // simple boolean flip — no checkbox.checked confusion

    all.forEach(cb => {
        cb.checked = allSelected;
        cb.dispatchEvent(new Event('change'));
    });

    renderSelectAllBox();
}

// Update the visual fake-checkbox in the header
function renderSelectAllBox() {
    const total   = document.querySelectorAll('.contact-checkbox').length;
    const checked = document.querySelectorAll('.contact-checkbox:checked').length;

    const hashLabel    = document.getElementById('hashLabel');
    const box          = document.getElementById('selectAllBox');
    const tick         = document.getElementById('selectAllTick');
    const dash         = document.getElementById('selectAllDash');

    if (checked === 0) {
        // Nothing selected — show hash, hide box
        hashLabel.classList.remove('hidden');
        box.classList.add('hidden');
        box.classList.remove('flex');
        tick.classList.add('hidden');
        dash.classList.add('hidden');
        box.classList.remove('border-teal-500');
        box.classList.add('border-slate-400');
    } else {
        // Something selected — show box, hide hash
        hashLabel.classList.add('hidden');
        box.classList.remove('hidden');
        box.classList.add('flex');
        box.classList.remove('border-slate-400');
        box.classList.add('border-teal-500');

        if (checked === total) {
            // All selected — show tick
            tick.classList.remove('hidden');
            dash.classList.add('hidden');
        } else {
            // Some selected — show dash (indeterminate)
            tick.classList.add('hidden');
            dash.classList.remove('hidden');
        }
    }
}

// Show/hide the floating bulk bar
function updateBulkBar() {
    const bar = document.getElementById('bulkActionBar');
    document.getElementById('selectedCount').textContent = selectedIds.size;
    if (selectedIds.size > 0) {
        bar.classList.remove('hidden');
        bar.classList.add('flex');
    } else {
        bar.classList.add('hidden');
        bar.classList.remove('flex');
    }
}

// Clear all selections
function clearSelection() {
    allSelected = false;
    document.querySelectorAll('.contact-checkbox').forEach(cb => {
        if (cb.checked) {
            cb.checked = false;
            cb.dispatchEvent(new Event('change'));
        }
    });
    selectedIds.clear();
    renderSelectAllBox();
    updateBulkBar();
}

// Open bulk delete confirm modal
function openBulkDeleteModal() {
    document.getElementById('bulkDeleteCount').textContent = selectedIds.size;
    document.getElementById('bulkDeleteModal').classList.remove('hidden');
    document.getElementById('bulkDeleteModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeBulkDeleteModal() {
    document.getElementById('bulkDeleteModal').classList.add('hidden');
    document.getElementById('bulkDeleteModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Execute the bulk delete AJAX call
function executeBulkDelete() {
    if (selectedIds.size === 0) return;

    // Show spinner
    document.getElementById('bulkDeleteBtnText').textContent = 'Deleting...';
    document.getElementById('bulkDeleteSpinner').classList.remove('hidden');

    fetch("{{ route('app.contact.deleteSelected') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            group_id: '{{ $groupId }}',
            selected: JSON.stringify([...selectedIds])
        })
    })
    .then(res => {
        if (res.ok) {
            window.location.reload();
        } else {
            alert('Something went wrong. Please try again.');
            document.getElementById('bulkDeleteBtnText').textContent = 'Yes, Delete';
            document.getElementById('bulkDeleteSpinner').classList.add('hidden');
        }
    })
    .catch(() => {
        alert('Network error. Please try again.');
        document.getElementById('bulkDeleteBtnText').textContent = 'Yes, Delete';
        document.getElementById('bulkDeleteSpinner').classList.add('hidden');
    });
}


// ── Import Modal ───────────────────────────────────────────────────────────
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
    document.getElementById('importModal').classList.add('flex');
}
function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.getElementById('importModal').classList.remove('flex');
}

// ── Add Contact Modal ──────────────────────────────────────────────────────
function openContactModal() {
    document.getElementById('contactModal').classList.remove('hidden');
    document.getElementById('contactModal').classList.add('flex');
}
function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
    document.getElementById('contactModal').classList.remove('flex');
}

// ── Edit Modal ─────────────────────────────────────────────────────────────
function openEditModal(contact) {
    document.getElementById('edit-id').value            = contact.id;
    document.getElementById('edit-group-id').value      = contact.group_id;
    document.getElementById('edit-email').value         = contact.email        || '';
    document.getElementById('edit-firstname').value     = contact.firstname    || '';
    document.getElementById('edit-lastname').value      = contact.lastname     || '';
    document.getElementById('edit-sms').value           = contact.sms         || '';
    document.getElementById('edit-whatsapp').value      = contact.whatsapp    || '';
    document.getElementById('edit-opt-in').value        = contact.opt_in      || 'Yes';
    document.getElementById('edit-double-opt-in').value = contact.double_opt_in || 'No';

    const modal = document.getElementById('editContactModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeEditModal() {
    const modal = document.getElementById('editContactModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// ── Single Delete Modal ────────────────────────────────────────────────────
function openDeleteModal(contactId) {
    document.getElementById('deleteContactId').value = contactId;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// ── Import Tab Switching ───────────────────────────────────────────────────
function switchTab(type) {
    const hybridContent = document.getElementById('content-hybrid');
    const googleContent = document.getElementById('content-google');
    const hybridTab     = document.getElementById('tab-hybrid');
    const googleTab     = document.getElementById('tab-google');

    if (type === 'hybrid') {
        hybridContent.classList.remove('hidden');
        googleContent.classList.add('hidden');
        hybridTab.classList.add('bg-white', 'shadow-sm', 'text-teal-700');
        hybridTab.classList.remove('text-slate-500');
        googleTab.classList.remove('bg-white', 'shadow-sm', 'text-blue-600');
        googleTab.classList.add('text-slate-500');
    } else {
        googleContent.classList.remove('hidden');
        hybridContent.classList.add('hidden');
        googleTab.classList.add('bg-white', 'shadow-sm', 'text-blue-600');
        googleTab.classList.remove('text-slate-500');
        hybridTab.classList.remove('bg-white', 'shadow-sm', 'text-teal-700');
        hybridTab.classList.add('text-slate-500');
    }
}

// ── File Upload with Drag & Drop ───────────────────────────────────────────
function setupFileUpload(inputId, dropZoneId, fileNameId, formId, color) {
    const input           = document.getElementById(inputId);
    const dropZone        = document.getElementById(dropZoneId);
    const fileNameDisplay = document.getElementById(fileNameId);
    const form            = document.getElementById(formId);

    if (!input || !dropZone || !fileNameDisplay || !form) return;

    dropZone.addEventListener('click', () => { input.click(); });

    input.addEventListener('change', () => {
        if (input.files.length > 0) {
            fileNameDisplay.textContent = input.files[0].name;
            fileNameDisplay.classList.add('text-' + color + '-600');
            setTimeout(() => { form.submit(); }, 500);
        }
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault(); e.stopPropagation();
        dropZone.classList.add('border-' + color + '-400', 'bg-' + color + '-50');
    });
    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault(); e.stopPropagation();
        dropZone.classList.remove('border-' + color + '-400', 'bg-' + color + '-50');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault(); e.stopPropagation();
        dropZone.classList.remove('border-' + color + '-400', 'bg-' + color + '-50');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            input.files = files;
            fileNameDisplay.textContent = files[0].name;
            fileNameDisplay.classList.add('text-' + color + '-600');
            setTimeout(() => { form.submit(); }, 500);
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    setupFileUpload('file-upload-input-hybrid', 'file-upload-select-hybrid', 'file-select-name-hybrid', 'import_form_hybrid', 'teal');
    setupFileUpload('file-upload-input-google', 'file-upload-select-google', 'file-select-name-google', 'import_form_google', 'blue');
});

// ── Close modals on backdrop click ────────────────────────────────────────
window.addEventListener('click', function (event) {
    if (event.target.id === 'importModal')       closeImportModal();
    if (event.target.id === 'contactModal')      closeContactModal();
    if (event.target.id === 'editContactModal')  closeEditModal();
    if (event.target.id === 'deleteModal')       closeDeleteModal();
    if (event.target.id === 'bulkDeleteModal')   closeBulkDeleteModal();
});

// ── ESC closes all modals ─────────────────────────────────────────────────
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeImportModal();
        closeContactModal();
        closeEditModal();
        closeDeleteModal();
        closeBulkDeleteModal();
    }
});

// ── Email validation ──────────────────────────────────────────────────────
document.getElementById('modal-email').addEventListener('input', function () {
    const email = this.value;
    const error = document.getElementById('modal-email-error');
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === '')          { error.textContent = 'Email is required.'; }
    else if (!regex.test(email)) { error.textContent = 'Please enter a valid email.'; }
    else                       { error.textContent = ''; }
});

// ── Toggle advanced fields ─────────────────────────────────────────────────
function toggleAdvancedFields() {
    const fields = document.getElementById('advancedFields');
    const text   = document.getElementById('toggleText');
    const icon   = document.getElementById('toggleIcon');
    if (fields.classList.contains('hidden')) {
        fields.classList.remove('hidden');
        text.innerText        = 'Hide advanced settings';
        icon.style.transform  = 'rotate(180deg)';
    } else {
        fields.classList.add('hidden');
        text.innerText        = 'Show advanced settings';
        icon.style.transform  = 'rotate(0deg)';
    }
}
</script>
@endsection
