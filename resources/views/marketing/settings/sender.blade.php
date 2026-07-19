@extends('marketing.layouts.app')

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css">

@section('content')
<div class="w-full min-h-screen bg-[#f8fafc] px-4 py-8 md:px-12 text-slate-900">
    
    {{-- Floating Header Section --}}
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Sender Domains</h1>
            <p class="text-slate-500 mt-1.5">Authenticate and manage your sending domains.</p>
        </div>
        
        <div class="flex items-center gap-4">
            <button onclick="showGuide()" class="inline-flex items-center justify-center rounded-xl text-sm font-bold border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 h-11 px-6 transition-all active:scale-95 shadow-sm">
                <i class="hgi hgi-stroke hgi-presentation-01 mr-2"></i>
                Start Tour
            </button>
            <button onclick="window.location.reload()" class="flex items-center gap-3 px-6 py-2 h-11 rounded-lg text-white bg-gradient-to-br from-[#007682] to-[#408b86] hover:brightness-110 transition-all duration-300 active:scale-95 shadow-lg">
                <i class="hgi hgi-stroke hgi-refresh text-lg"></i>
                <span class="font-bold text-sm">Refresh Status</span>
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto space-y-8">
        {{-- Alerts --}}
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                <i class="hgi hgi-stroke hgi-tick-01 text-lg"></i>
                <span class="text-sm font-bold uppercase tracking-wide">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                <i class="hgi hgi-stroke hgi-alert-circle text-lg"></i>
                <span class="text-sm font-bold uppercase tracking-wide">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Add Domain Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden add-domain">
            <div class="px-8 py-4 bg-slate-50 border-b flex justify-between items-center">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                    <i class="hgi hgi-stroke hgi-plus-01 text-teal-600"></i> Register New Domain
                </h3>
            </div>
            <div class="p-8">
                <form method="POST" action="{{route('app.setting.sender.save')}}" class="flex flex-col md:flex-row items-end gap-6">
                    @csrf
                    <div class="flex-1 space-y-2 w-full">
                        <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block tracking-wider">
                            Domain Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="domain" id="domain" placeholder="e.g. yourcompany.com" required
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                    </div>
                    <div class="flex flex-col items-end gap-3">
                        @if(count($data) >= (auth()->user()->role == 2 ? 3 : 5))
                            <p class="text-[10px] font-bold text-red-500 uppercase tracking-tight">
                                {{auth()->user()->role == 2 ? 'STANDARD' : 'ENTERPRISE'}} plan limit reached ({{count($data)}}/{{auth()->user()->role == 2 ? 3 : 5}})
                            </p>
                        @endif
                        <button type="submit" 
                                {{ count($data) >= (auth()->user()->role == 2 ? 3 : 5) ? 'disabled' : '' }}
                                class="px-8 py-3 rounded-xl text-white font-bold text-sm bg-slate-900 hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95 shadow-md">
                            Add Domain
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Domains Table/List --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden dns-detail">
            <div class="px-8 py-4 bg-slate-50 border-b">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                    <i class="hgi hgi-stroke hgi-database-02 text-teal-600"></i> Active DNS Configurations
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">No</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Domain</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">DNS Records (Add to your Registrar)</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @if(!empty($data) && count($data))
                            @foreach($data as $key => $value)
                                @php $dns = json_decode($value['dns']); @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-6 align-top font-bold text-slate-400 text-sm">{{ $key + 1 }}</td>
                                    <td class="px-8 py-6 align-top">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg bg-teal-50 text-teal-700 text-sm font-bold border border-teal-100">
                                            {{ $value['domain'] }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 space-y-4">
                                        {{-- Record Loop --}}
                                        @foreach(['mail_cname', 'dkim1', 'dkim2'] as $recordKey)
                                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 relative group">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-[9px] font-black text-teal-600 uppercase tracking-widest bg-white px-2 py-0.5 rounded border border-teal-100">
                                                        {{ strtoupper($dns->$recordKey->type) }} Record
                                                    </span>
                                                    <div class="flex items-center gap-2">
                                                        @if($dns->$recordKey->valid)
                                                            <span class="text-[9px] font-bold text-emerald-600 uppercase italic">Verified</span>
                                                            <i class="hgi hgi-stroke hgi-tick-01 text-emerald-500 text-xs"></i>
                                                        @else
                                                            <span class="text-[9px] font-bold text-amber-500 uppercase italic">Pending</span>
                                                            <i class="hgi hgi-stroke hgi-loading-01 text-amber-500 text-xs animate-spin"></i>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    {{-- Host Copy --}}
                                                    <div class="cursor-pointer group/item" onclick="copyClipboard('{{$recordKey}}_host')">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Host / Name</p>
                                                        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-lg px-3 py-2">
                                                            <span class="text-xs font-mono font-bold text-slate-700 truncate mr-2">{{$dns->$recordKey->host}}</span>
                                                            <i class="hgi hgi-stroke hgi-copy-01 text-slate-400 group-hover/item:text-teal-500 text-sm"></i>
                                                        </div>
                                                        <input id="{{$recordKey}}_host" class="hidden" value="{{$dns->$recordKey->host}}"/>
                                                        <span id="{{$recordKey}}_host_copied" class="hidden text-[9px] font-black text-teal-500 uppercase mt-1">Copied!</span>
                                                    </div>

                                                    {{-- Value Copy --}}
                                                    <div class="cursor-pointer group/item" onclick="copyClipboard('{{$recordKey}}_value')">
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Value / Points To</p>
                                                        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-lg px-3 py-2">
                                                            <span class="text-xs font-mono font-bold text-slate-700 truncate mr-2">{{$dns->$recordKey->data}}</span>
                                                            <i class="hgi hgi-stroke hgi-copy-01 text-slate-400 group-hover/item:text-teal-500 text-sm"></i>
                                                        </div>
                                                        <input id="{{$recordKey}}_value" class="hidden" value="{{$dns->$recordKey->data}}"/>
                                                        <span id="{{$recordKey}}_value_copied" class="hidden text-[9px] font-black text-teal-500 uppercase mt-1">Copied!</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="px-8 py-6 align-top text-center">
                                        <button type="button" onclick="deleteDomain('{{ $value['sendgrid_id'] }}', '{{ $value['domain'] }}')"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-red-100 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-90 shadow-sm">
                                            <i class="hgi hgi-stroke hgi-delete-02"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="hgi hgi-stroke hgi-folder-not-found text-4xl text-slate-200 mb-4"></i>
                                        <p class="text-slate-400 font-bold text-sm uppercase tracking-widest">No domains found</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>
<script>
    // Logic remains same, but using the updated UI references
    function copyClipboard(copyEle) {
        var copyText = document.getElementById(copyEle);
        navigator.clipboard.writeText(copyText.value);

        var label = document.getElementById(copyEle + '_copied');
        label.classList.remove('hidden');

        setTimeout(function() {
            label.classList.add('hidden');
        }, 2000);
    }

    function deleteDomain(sendgridId, domain) {
        if (confirm('Delete domain "' + domain + '"? This cannot be undone.')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("sender.delete") }}';
            
            var csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            var sendgridIdInput = document.createElement('input');
            sendgridIdInput.type = 'hidden';
            sendgridIdInput.name = 'sendgrid_id';
            sendgridIdInput.value = sendgridId;
            form.appendChild(sendgridIdInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // showGuide() function as defined in your original snippet
    function showGuide() {
        introJs().setOptions({
            steps: [
                { title: 'Welcome', intro: 'Connecting your own domain name 👋' },
                { title: 'Request domain', element: document.querySelector('.add-domain'), intro: 'Add your domain name in this section.' },
                { title: 'DNS detail', element: document.querySelector('.dns-detail'), intro: 'Copy these records to your domain provider.' }
            ]
        }).start();
    }
</script>