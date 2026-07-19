@extends('marketing.layouts.app')
<title>ACCOUNT : Settings</title>

@section('content')
<div class="w-full min-h-screen bg-slate-50/50 p-8 text-slate-900">
    <div class="max-w-7xl mx-auto">
        
        <div class="mb-10">
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Account Settings</h1>
            <p class="text-slate-500 font-medium">Manage your preferences, authenticate domains, and optimize deliverability.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="group flex flex-col items-center text-center bg-white border border-slate-200 p-8 rounded-xl shadow-sm hover:shadow-xl hover:border-teal-500 transition-all duration-300 transform hover:-translate-y-2">
                <div class="w-16 h-16 rounded-2xl bg-teal-50 border border-teal-100 text-teal-600 flex items-center justify-center mb-6 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
                    <i class="hgi hgi-stroke hgi-settings-02 text-2xl"></i>
                </div>
                <h4 class="text-lg font-black text-slate-900 mb-3 uppercase tracking-tight">General</h4>
                <p class="text-slate-600 opacity-75 text-sm leading-relaxed mb-8 flex-grow">
                    Account preferences and default campaign metadata.
                </p>
                <a href="{{ route('app.setting.default') }}" class="w-full py-3 bg-slate-50 group-hover:bg-teal-600 text-slate-500 group-hover:text-white rounded-full font-black text-[10px] uppercase tracking-[0.1em] transition-all duration-300 border border-slate-200 group-hover:border-teal-600 shadow-sm">
                    Configure
                </a>
            </div>

            <div class="group flex flex-col items-center text-center bg-white border border-slate-200 p-8 rounded-xl shadow-sm hover:shadow-xl hover:border-emerald-500 transition-all duration-300 transform hover:-translate-y-2">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                    <i class="hgi hgi-stroke hgi-user-group text-2xl"></i>
                </div>
                <h4 class="text-lg font-black text-slate-900 mb-3 uppercase tracking-tight">Test List</h4>
                <p class="text-slate-600 opacity-75 text-sm leading-relaxed mb-8 flex-grow">
                    Internal segments for campaign quality assurance.
                </p>
                <a href="{{ route('app.group.create') }}" class="w-full py-3 bg-slate-50 group-hover:bg-emerald-600 text-slate-500 group-hover:text-white rounded-full font-black text-[10px] uppercase tracking-[0.1em] transition-all duration-300 border border-slate-200 group-hover:border-emerald-600 shadow-sm">
                    Set up List
                </a>
            </div>

            <div class="group flex flex-col items-center text-center bg-white border border-slate-200 p-8 rounded-xl shadow-sm hover:shadow-xl hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-2">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <i class="hgi hgi-stroke hgi-security-check text-2xl"></i>
                </div>
                <h4 class="text-lg font-black text-slate-900 mb-3 uppercase tracking-tight">Authentication</h4>
                 <p class="text-slate-600 opacity-75 text-sm leading-relaxed mb-8 flex-grow">
                    Manage sender identity and SPF/DKIM protocols.
                </p>
                <a href="{{route('app.setting.sender')}}" onclick="onClickSendorDomains(event)" class="w-full py-3 bg-slate-50 group-hover:bg-blue-600 text-slate-500 group-hover:text-white rounded-full font-black text-[10px] uppercase tracking-[0.1em] transition-all duration-300 border border-slate-200 group-hover:border-blue-600 shadow-sm">
                    Manage DNS
                </a>
            </div>

            <div class="group flex flex-col items-center text-center bg-white border border-slate-200 p-8 rounded-xl shadow-sm hover:shadow-xl hover:border-orange-500 transition-all duration-300 transform hover:-translate-y-2">
                <div class="w-16 h-16 rounded-2xl bg-orange-50 border border-orange-100 text-orange-600 flex items-center justify-center mb-6 group-hover:bg-orange-600 group-hover:text-white transition-colors duration-300">
                    <i class="hgi hgi-stroke hgi-location-01 text-2xl"></i>
                </div>
                <h4 class="text-lg font-black text-slate-900 mb-3 uppercase tracking-tight">Infrastructure</h4>
                 <p class="text-slate-600 opacity-75 text-sm leading-relaxed mb-8 flex-grow">
                    Dedicated IP management for high-volume senders.
                </p>
                <a href="https://nordvpn.com/features/dedicated-ip/" target="_blank" class="w-full py-3 bg-slate-50 group-hover:bg-orange-600 text-slate-500 group-hover:text-white rounded-full font-black text-[10px] uppercase tracking-[0.1em] transition-all duration-300 border border-slate-200 group-hover:border-orange-600 shadow-sm">
                    Get Dedicated IP
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function onClickSendorDomains(event) {
        var level = {{ auth()->user()->role }};
        if(level == 1) {
            event.preventDefault();
            // Using your existing sweetalert if available, otherwise native alert
            if (typeof swal === "function") {
                swal({
                    title: "Premium Feature",
                    text: "Please upgrade your account plan to use custom domain names.",
                    icon: "info",
                    button: "Upgrade Now",
                }).then(() => {
                    $("#upgradeModal").modal('show');
                });
            } else {
                alert('Please upgrade your account plan to use custom domain names.');
                $("#upgradeModal").modal('show');
            }
        }
    }
</script>
@endsection