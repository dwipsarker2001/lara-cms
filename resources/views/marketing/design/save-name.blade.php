@extends('marketing.layouts.app')

@section('content')
<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
    
    <div class="w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden relative animate-in fade-in zoom-in-95 duration-200">
        <div class="relative bg-white rounded-2xl shadow-lg border border-slate-100">
            
            <div class="text-center pt-10 pb-6 px-8 bg-gradient-to-b from-[#007682]/10 via-[#408b86]/5 to-white">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-teal-50">
                    <i class="hgi hgi-stroke hgi-floppy-disk text-2xl text-teal-600"></i>
                </div>
                <h2 class="text-xl font-black text-slate-900 mb-1">
                    {{$action_type == "new" ? "Save Template" : "Update Template"}}
                </h2>
                <p class="text-sm text-slate-500">Give your design a name to save it to your library.</p>
            </div>

            @if(session('error'))
                <div class="mx-6 mt-4 p-3 bg-red-50 border border-red-100 rounded-xl flex items-center gap-3 text-red-600">
                    <i class="hgi hgi-stroke hgi-alert-01 text-lg"></i>
                    <p class="text-xs font-bold">{{ session('error') }}</p>
                </div>
            @endif

            <form method="POST" action="{{route('app.template.store')}}" class="px-8 pb-8 mt-6 mb-0">
                @csrf
                <input name="action_type" value="{{$action_type}}" hidden/>
                <input name="template_id" value="{{$template_id}}" hidden/>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-slate-400 mb-2 ml-1">
                            Template Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="hgi hgi-stroke hgi-pencil-edit-01 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input 
                                id="name" 
                                type="text" 
                                name="name" 
                                required
                                placeholder="e.g. Monthly Newsletter"
                                value="{{$org_name}}"
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:border-teal-400 focus:ring-4 focus:ring-teal-500/10 focus:bg-white outline-none transition font-medium text-slate-700"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-8">
                    @if($action_type == "new")
                    <button type="submit" name="action" value="close" 
                        class="flex-1 order-2 sm:order-1 py-3.5 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold hover:bg-red-50 hover:text-red-500 transition-all">
                        Don't Save
                    </button>
                    @endif

                    <a href="{{url('design?id='. $template_id. '&type=user')}}" 
                       class="flex-1 order-3 sm:order-2 py-3.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all text-center">
                        Back to Editor
                    </a>

                    <button type="submit" name="action" value="save" 
                        class="flex-[1.5] order-1 sm:order-3 bg-gradient-to-br from-[#007682] to-[#408b86] py-3.5 rounded-xl text-white text-sm font-bold shadow-lg shadow-teal-900/20 hover:brightness-110 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="hgi hgi-stroke hgi-check-mark-circle-01 text-lg"></i>
                        {{$action_type == "new" ? "Save Now" : "Update Name"}}
                    </button>
                </div>
            </form>

            <div class="bg-slate-50/50 py-3 text-center border-t border-slate-50">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                    Action cannot be undone once deleted from library
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Focus the input field on load
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        nameInput.focus();
        // Move cursor to end of text
        const val = nameInput.value;
        nameInput.value = '';
        nameInput.value = val;
    });
</script>
@endsection
