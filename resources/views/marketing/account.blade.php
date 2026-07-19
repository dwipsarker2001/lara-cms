@extends('marketing.layouts.app')

@section('content')
<div class="w-full min-h-screen bg-[#f8fafc] px-4 py-8 md:px-12 text-slate-900">
    <form method="POST" action="{{route('app.account.store')}}">
        @csrf
        
        {{-- Page Header --}}
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Account Profile</h1>
                <p class="mt-1 text-lg text-slate-600">Update your identity, security protocols, and communication preferences.</p>
            </div>
            <div class="flex items-center gap-4">
                <button type="submit" class="flex items-center gap-3 px-8 py-3 rounded-lg text-white bg-gradient-to-br from-[#007682] to-[#408b86] hover:brightness-110 transition-all duration-300 shadow-lg shadow-teal-900/10 active:scale-95">
                    <i class="hgi hgi-stroke hgi-mail-add-02 text-lg"></i>
                    <span class="font-bold text-sm">Create New Campaign</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="max-w-7xl mx-auto mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                <i class="hgi hgi-stroke hgi-tick-01 text-lg"></i>
                <span class="text-sm font-bold uppercase tracking-wide">{{ session('success') }}</span>
            </div>
        @endif

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Column --}}
            <div class="lg:col-span-7 space-y-6">
                
                {{-- Personal Details Section --}}
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-4 bg-slate-50 border-b flex justify-between items-center">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <i class="hgi hgi-stroke hgi-user text-teal-600"></i> Personal Details
                        </h3>
                    </div>
                    
                    <div class="p-8 space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">Title / Occupation</label>
                            <input name="title" value="{{($profile == NULL || $profile->title == '') ? session('hybrid_user_name') : $profile->title}}" required
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">First Name</label>
                                <input name="firstname" value="{{$profile == NULL ? '' : $profile->firstname}}" required
                                    class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">Last Name</label>
                                <input name="lastname" value="{{$profile == NULL ? '' : $profile->lastname}}" required
                                    class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">Email Address</label>
                                <input type="email" name="email" value="{{($profile == NULL || $profile->email == '') ? session('userEmail') : $profile->email}}" required
                                    class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">Telephone</label>
                                <input name="telephone" value="{{$profile == NULL ? '' : $profile->telephone}}" required
                                    class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Security Section --}}
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-4 bg-slate-50 border-b flex justify-between items-center">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <i class="hgi hgi-stroke hgi-security-check text-teal-600"></i> Authentication & Security
                        </h3>
                    </div>
                    <div class="p-8">
                        {{-- Info Alert matching your blue style --}}
                        <div class="bg-blue-50/50 border border-blue-100 rounded-lg p-4 mb-8 flex gap-3">
                            <i class="hgi hgi-stroke hgi-information-circle text-blue-600 mt-0.5"></i>
                            <p class="text-xs text-blue-800 font-medium leading-relaxed">
                                Enhancing your password strength with symbols, numbers, and casing significantly improves your deliverability and account safety.
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">New Password</label>
                                <input type="password" name="new_password" 
                                    class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm" placeholder="••••••••">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">Confirm Password</label>
                                <input type="password" name="confirm_password" 
                                    class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm" placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Sidebar --}}
            <div class="lg:col-span-5 lg:sticky lg:top-8 space-y-6">
                
                {{-- Account Settings --}}
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-4 bg-slate-50 border-b">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <i class="hgi hgi-stroke hgi-settings-02 text-teal-600"></i> Account Settings
                        </h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">Account Type</label>
                            <select name="account_type" onchange="changeType(this)" 
                                class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm appearance-none cursor-pointer">
                                <option value="individual" {{$profile && $profile->account_type == 'individual' ? 'selected' : ''}}>Individual Professional</option>
                                <option value="company"  {{$profile && $profile->account_type == 'company' ? 'selected' : ''}}>Enterprise / Company</option>
                            </select>
                        </div>

                        <div class="company-form space-y-6 {{$profile && $profile->account_type == 'individual' ? 'hidden' : ''}} animate-in fade-in slide-in-from-top-2">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">Company Name</label>
                                <input name="company_name" value="{{$profile == NULL ? '' : $profile->company_name}}" 
                                    class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">Industry</label>
                                <input name="industry" value="{{$profile == NULL ? '' : $profile->industry}}" 
                                    class="w-full px-4 py-3 rounded-lg border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Communication Preferences --}}
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-4 bg-slate-50 border-b">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <i class="hgi hgi-stroke hgi-notification-01 text-teal-600"></i> Communication
                        </h3>
                    </div>
                    <div class="p-8">
                        <p class="text-xs text-slate-500 font-medium leading-relaxed mb-6">
                            Stay updated with exclusive offers, vouchers, and deals. Toggle preferences below.
                        </p>
                        
                        <div class="space-y-4">
                            @foreach(['is_email' => 'Email Updates', 'is_phone' => 'Phone Calls', 'is_sms' => 'SMS Alerts', 'is_post' => 'Post / Mail'] as $key => $label)
                            <div class="flex items-center justify-between py-3 border-b border-slate-50 last:border-0">
                                <span class="text-sm font-bold text-slate-700">{{ $label }}</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="{{ $key }}" id="{{ $key }}" class="sr-only peer" 
                                        {{($profile == NULL || ($key == 'is_email' ? $profile->$key : $profile->$key)) ? 'checked' : ''}}>
                                    <div class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:bg-teal-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full"></div>
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-100 flex items-center gap-2">
                            <i class="hgi hgi-stroke hgi-privacy text-slate-400"></i>
                            <p class="text-[10px] text-slate-400 leading-relaxed font-bold uppercase tracking-tight">
                                Secured data. Review our <a href="#" class="text-teal-600 hover:underline">Privacy Policy</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    function changeType(obj) {
        const companyForms = document.querySelectorAll('.company-form');
        companyForms.forEach(el => {
            if(obj.value === 'company'){
                el.classList.remove('hidden');
                el.classList.add('flex', 'flex-col');
            } else {
                el.classList.add('hidden');
                el.classList.remove('flex', 'flex-col');
            }
        });
    }
</script>
@endsection