@extends('marketing.layouts.app')

<title>ACCOUNT : CONFIRM IMPORT</title>

@section('content')
<div class="w-full min-h-screen bg-slate-50/50 px-8 py-8">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Confirm Contacts</h1>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <p class="text-slate-500 text-sm leading-relaxed max-w-md">
                    Review and map your contact list before finalizing the import.
                </p>
            </div>
        </div>

        
        
        <div class="flex items-center gap-3">

            <!-- Back Button -->
            <a href="{{ route('app.contact.index', $groupId) }}" 
                class="flex items-center justify-center gap-2 px-8 py-2.5 
                    rounded-xl border border-slate-200 
                    bg-white text-slate-600 
                    font-bold text-sm 
                    hover:bg-slate-50 transition-all shadow-sm">
                <i class="hgi hgi-stroke hgi-arrow-left-01 text-lg"></i>
                Back
            </a>

            <!-- Confirm & Upload -->
            <form class="m-0" method="post" action="{{ route('app.contact.upload', $groupId) }}">
                @csrf
                <input name="type" value="{{$type}}" hidden/>
                <input name="filename" value="{{ $filename }}" hidden />
                
                <button type="submit" 
                    class="flex items-center justify-center gap-2 px-8 py-2.5 
                        rounded-xl text-white 
                        bg-gradient-to-br from-[#007682] to-[#408b86] 
                        hover:brightness-110 transition-all 
                        shadow-teal-900/20 active:scale-95 
                        font-bold text-sm">
                    <i class="hgi hgi-stroke hgi-cloud-upload text-lg"></i>
                    Confirm & Upload
                </button>
            </form>
        </div>
    </div>

    <div class="mb-4 p-5 bg-[#FFF9EE] border border-[#F6E1B7aa] rounded-lg shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 bg-[#FFB822] rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="hgi hgi-stroke hgi-information-circle text-white text-xl"></i>
            </div>
            
            <div>
                <p class="text-[15px] font-bold text-slate-800 leading-tight">Review Required</p>
                <p class="text-[13px] text-slate-500 font-medium mt-0.5">Duplicate email addresses will be ignored automatically.</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden text-nowrap">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="border-b border-slate-200 bg-gradient-to-r from-[#f0f9f9] via-[#e6f2f2] to-[#d1e6e6]">
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-3.5 w-12 text-center"><span class="font-semibold text-slate-500">#</span></th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Email Identity</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Full Name</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Channels</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Opt-In</th>
                    </tr>
                </thead>
              <tbody class="divide-y divide-slate-200">
                    @forelse($data as $key => $value)
                        @php
                            $email = ($type == 'hybrid') ? ($value[0] ?? '') : ($value[30] ?? '');
                            $fname = ($type == 'hybrid') ? ($value[2] ?? '') : ($value[3] ?? '');
                            $lname = ($type == 'hybrid') ? ($value[1] ?? '') : ($value[1] ?? '');
                        @endphp

                        @if($email != '')
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-bold text-slate-300">
                                    <div>
                                        <i class="hgi hgi-stroke hgi-mail-01 text-[#1f8084] text-lg"></i>
                                    </div>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-600 hover:text-teal-600 transition-colors block mb-1 truncate">{{ $email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600 font-medium">{{ $fname }} {{ $lname }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($type == 'hybrid')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ !empty($value[3]) ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-500' }}">SMS</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ !empty($value[4]) ? 'bg-teal-700 text-white' : 'bg-slate-100 text-slate-500' }}">WA</span>
                                    @else
                                        <span class="text-[12px] font-semibold text-slate-500">Google Import</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-black {{ ($value[6] ?? '') == 'Yes' ? 'text-teal-600' : 'text-slate-400' }}">
                                    {{ $value[6] ?? 'No' }}
                                </span>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">No data detected.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
