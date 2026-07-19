@extends('marketing.layouts.app')

@section('content')
<div class="w-full min-h-screen bg-slate-50/50 px-8 py-8 text-slate-900">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Form Builder</h1>
                <p class="text-slate-500 mt-1.5">Create and manage data collection forms for your campaigns.</p>
            </div>
            
            <a href="{{ url('form/create') }}"
                class="flex items-center gap-3 px-6 py-2 rounded-lg text-white 
                    bg-gradient-to-br from-[#007682] to-[#408b86]
                    hover:brightness-110
                    transition-all duration-300 active:scale-95
                    shadow-lg">
                <i class="hgi hgi-stroke hgi-file-add text-lg"></i>
                <span class="font-bold text-sm">Create New Form</span>
            </a>
        </div>


        <div class="mb-4 p-5 bg-[#FFF9EE] border border-[#F6E1B7aa] rounded-lg shadow-sm flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-[#FFB822] rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class="hgi hgi-stroke hgi-information-circle text-white text-xl"></i>
                </div>
                
                <div>
                    <p class="text-[15px] font-bold text-slate-800 leading-tight">Deployment Guide</p>
                    <p class="text-[13px] text-slate-500 font-medium mt-0.5">
                        Once published, forms are visible at a unique Public Link Embed these links into buttons within your HTML Builder to capture leads.
                    </p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 px-6 py-4 rounded-2xl border border-emerald-100 shadow-sm mb-8 flex items-center gap-3">
                <i class="hgi hgi-stroke hgi-tick-01 text-lg"></i>
                <span class="text-sm font-bold uppercase tracking-wide">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Forms Table --}}
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden text-nowrap">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="border-b border-slate-200 bg-gradient-to-r from-[#f0f9f9] via-[#e6f2f2] to-[#d1e6e6]">
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-3.5 w-12 text-center"><span class="font-semibold text-slate-500">#</span></th>
                            <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Form Identity</th>
                            <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Public Endpoint</th>
                            <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">System File</th>
                            <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($data as $value)
                        <tr class="transition-all duration-200 ease-out hover:bg-slate-50/80">
                            <td class="px-6 py-5 text-center">
                                <div>
                                    <i class="hgi hgi-stroke hgi-file-02 text-[#1f8084] text-xl"></i>
                                </div>
                            </td>
                            
                            <td class="px-6 py-5">
                                <a href="{{ url('form/edit/'. $value->id) }}" class="text-sm font-semibold text-slate-600 hover:text-teal-600 transition-colors block mb-1">
                                    {{ $value->name }}
                                </a>
                                <span class="text-[11px] text-slate-500 font-medium block uppercase tracking-tighter">
                                    MODIFIED {{ $value->updated_at->format('d M, Y') }}
                                </span>
                            </td>

                            <td class="px-6 py-5">
                                <span class="text-[12px] text-slate-500 font-medium block">Public Link</span>
                                <div class="flex flex-col truncate">
                                    <span class="text-[12px] font-semibold text-slate-600 truncate tracking-tight">
                                        {{ request()->getHost() }}/form/{{ $value->path }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-5">
                                <span class="text-[12px] text-slate-500 font-medium block">File Name</span>
                                <div class="flex flex-col truncate">
                                    <span class="text-[12px] font-semibold text-slate-600 truncate tracking-tight">
                                        {{ $value->path }}.json
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-5">
                                <div class="flex justify-end items-center">
                                    <a target="_blank" href="{{ url('public/forms/form_'. $value->path. '.php') }}" 
                                        class="p-2 hover:bg-slate-100 text-slate-600 hover:text-slate-900 rounded-lg transition-all" title="Live Preview">
                                        <i class="hgi hgi-stroke hgi-view text-lg"></i>
                                    </a>
                                    <a href="{{ url('form/edit/'. $value->id) }}" 
                                        class="p-2 hover:bg-slate-100 text-slate-600 hover:text-slate-900 rounded-lg transition-all" title="Edit Design">
                                        <i class="hgi hgi-stroke hgi-pencil-edit-02 text-lg"></i>
                                    </a>
                                    <button onclick="openDeleteModal({{ $value->id }}, '{{ $value->path }}')" 
                                            class="p-2 hover:bg-red-50 text-slate-600 hover:text-red-500 rounded-lg transition-all" title="Delete Form">
                                        <i class="hgi hgi-stroke hgi-delete-02 text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-24 text-center">
                                <div class=" mx-auto">
                                    <i class="hgi hgi-stroke hgi-file-02 text-5xl text-slate-300 mb-4 block"></i>
                                    <h3 class="text-lg font-bold text-slate-900 mb-1">No forms found</h3>
                                    <p class="text-sm text-slate-500 mb-6 font-medium">Capture better leads by building your first collection form today.</p>
                                    <button onclick="window.location.href='{{ url('form/create') }}'" class="text-sm font-bold text-teal-600 hover:underline">Get Started Now &rarr;</button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination & Footer --}}
        <div class="mt-8 flex justify-end">
            {!! $data->links() !!}
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal (Campaigns Style) --}}
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-xl text-center">
        <div class="w-14 h-14 mx-auto flex items-center justify-center rounded-full bg-red-100 mb-4">
            <i class="hgi hgi-stroke hgi-delete-02 text-2xl text-red-600"></i>
        </div>
        <h2 class="text-lg font-semibold text-slate-800 mb-2">Delete Form?</h2>
        <p class="text-sm text-slate-500 mb-6">This action will permanently remove the form and its data endpoint.</p>
        
        <form id="deleteForm" method="post" action="{{route('app.form.delete')}}">
            @csrf
            <input type="hidden" name="id" id="deleteFormId" />
            <input type="hidden" name="path" id="deleteFormPath" />
            
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

@endsection

@section('script')
<script>
    function openDeleteModal(formId, formPath) {
        document.getElementById('deleteFormId').value = formId;
        document.getElementById('deleteFormPath').value = formPath;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Backdrop click close
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    // ESC key close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>
@endsection
