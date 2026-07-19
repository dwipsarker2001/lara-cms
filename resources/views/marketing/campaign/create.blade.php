@extends('marketing.layouts.app')

@section('content')
<div class="w-full min-h-screen bg-slate-50/50 px-8 py-12">
    <div class="mb-8">
        <a href="{{ route('app.campaign.index') }}" class="group flex items-center gap-2 text-slate-400 hover:text-teal-600 transition-colors">
            <i class="hgi hgi-stroke hgi-arrow-left-01 text-lg group-hover:-translate-x-1 transition-transform"></i>
            <span class="text-xs font-bold uppercase tracking-widest">Back to Campaigns</span>
        </a>
    </div>



    <div class="max-w-2xl mx-auto">
        @if($rem_campaigns <= 0)
        <div class="mb-8 p-4 rounded-xl bg-red-50 border border-red-100 flex gap-4 items-center">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <i class="hgi hgi-stroke hgi-alert-circle text-red-600 text-xl"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-red-900">Campaign Limit Reached</h4>
                <p class="text-xs text-red-700/80">Upgrade your membership to launch more marketing campaigns. <a href="#" class="font-bold underline">View Plans</a></p>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="relative p-8 text-center border-b border-slate-100">
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none">
                    <i class="hgi hgi-stroke hgi-mail-send-01 text-[150px] absolute -right-10 -top-10"></i>
                </div>
                <div class="w-20 h-20 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-inner border border-slate-100">
                    <i class="hgi hgi-stroke hgi-mail-add-02 text-3xl text-teal-600"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Create Email Campaign</h1>
                <p class="text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">
                    Share your latest news, promote products, or announce an upcoming event to your subscribers.
                </p>
            </div>

            <form action="{{route('app.campaign.store')}}" method="post" class="p-8">
                @csrf
                <div class="mb-8">
                    <label for="name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                        Campaign Name
                    </label>
                    <div class="relative group">
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            maxlength="100" 
                            required
                            placeholder="e.g. Summer Flash Sale 2026"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 font-medium 
                                   placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-teal-500/20 
                                   focus:border-teal-500 transition-all duration-200"
                        />
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-teal-500 transition-colors">
                            <i class="hgi hgi-stroke hgi-cursor-edit-02 text-xl"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-[11px] text-slate-400 italic">This name is for internal use only and won't be seen by subscribers.</p>
                </div>

                <div class="flex flex-col gap-4">
                    <button type="submit" 
                        {{$rem_campaigns <= 0 ? 'disabled': ''}}
                        class="w-full flex items-center justify-center gap-3 px-8 py-4 rounded-xl text-white 
                            bg-gradient-to-br from-[#007682] to-[#408b86]
                            hover:brightness-110 disabled:grayscale disabled:opacity-50
                            transition-all duration-300 active:scale-[0.98]
                            shadow-lg shadow-teal-900/10">
                        
                        <span class="font-bold">Initialize Campaign</span>
                        <i class="hgi hgi-stroke hgi-arrow-right-01 text-lg"></i>
                    </button>
                    <p class="text-center text-[10px] text-slate-400 uppercase tracking-tighter">
                        Next Step: <span class="text-slate-600">Design your email template</span>
                    </p>
                </div>
            </form>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4">
            <div class="p-4 rounded-xl border border-dashed border-slate-200">
                <h5 class="text-xs font-bold text-slate-600 mb-1 flex items-center gap-2">
                    <i class="hgi hgi-stroke hgi-bulb text-teal-500"></i> Tip
                </h5>
                <p class="text-[11px] text-slate-400 leading-normal">Keep names descriptive like "Month_Year_Subject" to stay organized.</p>
            </div>
            <div class="p-4 rounded-xl border border-dashed border-slate-200">
                <h5 class="text-xs font-bold text-slate-600 mb-1 flex items-center gap-2">
                    <i class="hgi hgi-stroke hgi-shield-check text-indigo-500"></i> Limits
                </h5>
                <p class="text-[11px] text-slate-400 leading-normal">You have <span class="font-bold text-slate-700">{{ $rem_campaigns }}</span> campaign slots remaining this month.</p>
            </div>
        </div>
    </div>
</div>
@endsection
