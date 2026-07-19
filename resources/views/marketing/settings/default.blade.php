@extends('marketing.layouts.app')

@section('content')
<div class="w-full min-h-screen bg-[#f8fafc] px-4 py-8 md:px-12 text-slate-900">
    <form action="{{route('app.default.save')}}" method="post">
        @csrf
        
        {{-- Floating Header Section --}}
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Global Configuration</h1>
                <p class="text-slate-500 mt-1.5">Set your system-wide defaults for metadata and tracking.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{url('setting')}}" class="inline-flex items-center justify-center rounded-xl text-sm font-bold border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 h-11 px-6 transition-all active:scale-95 shadow-sm">
                    <i class="hgi hgi-stroke hgi-arrow-left-02 mr-2"></i>
                    Go Back
                </a>
				<button type="submit"  class="flex items-center gap-3 px-6 py-2 rounded-lg text-white 
						bg-gradient-to-br from-[#007682] to-[#408b86]
						hover:brightness-110
						transition-all duration-300 active:scale-95
						shadow-lg">
					<i class="hgi hgi-stroke hgi-tick-01 text-lg"></i>
					<span class="font-bold text-sm">Save Changes</span>
				</button>
            </div>
        </div>

        @if (session('success'))
            <div class="max-w-7xl mx-auto mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                <i class="hgi hgi-stroke hgi-tick-01 text-lg"></i>
                <span class="text-sm font-bold uppercase tracking-wide">{{ session('success') }}</span>
            </div>
        @endif

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Column: General & Tracking --}}
            <div class="lg:col-span-5 space-y-6">
                
                {{-- General Settings Section --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-4 bg-slate-50 border-b flex justify-between items-center">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <i class="hgi hgi-stroke hgi-settings-02 text-teal-600"></i> General Configuration
                        </h3>
                    </div>
                    
                    <div class="p-8 space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">System Timezone</label>
                            <select name="timezone" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm appearance-none cursor-pointer">
                                @foreach($world_timezone as $timezone)
                                <option value="{{$timezone['value']}}" {{$default_setting && ($default_setting->timezone ==  $timezone['value']) ? 'selected' : ''}}>{{$timezone['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center gap-2 mb-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block tracking-wider">Delay Time (Hours)</label>
                                <i class="hgi hgi-stroke hgi-information-circle text-teal-600 text-sm cursor-help" title="Define a timeframe to prevent multiple emails to same contact."></i>
                            </div>
                            <input type="number" name="delay_time" value="{{$default_setting ? $default_setting->delay_time : 0}}"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block tracking-wider">Time Format</label>
                                <div class="flex flex-col gap-3">
                                    <label class="relative inline-flex items-center cursor-pointer group">
                                        <input type="radio" name="time_format" value="24hours" class="sr-only peer" {{!$default_setting ? 'checked' : ($default_setting->time_format == '24hours' ? 'checked' : '')}}>
                                        <div class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-teal-500 peer-checked:bg-teal-500 transition-all mr-2"></div>
                                        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors">24 Hours</span>
                                    </label>
                                    <label class="relative inline-flex items-center cursor-pointer group">
                                        <input type="radio" name="time_format" value="12hours" class="sr-only peer" {{!$default_setting ? '' : ($default_setting->time_format == '12hours' ? 'checked' : '')}}>
                                        <div class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-teal-500 peer-checked:bg-teal-500 transition-all mr-2"></div>
                                        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors">12 Hours</span>
                                    </label>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block tracking-wider">Date Format</label>
                                <div class="flex flex-col gap-3">
                                    <label class="relative inline-flex items-center cursor-pointer group">
                                        <input type="radio" name="date_format" value="dd-mm-yyyy" class="sr-only peer" {{!$default_setting ? 'checked' : ($default_setting->date_format == 'dd-mm-yyyy' ? 'checked' : '')}}>
                                        <div class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-teal-500 peer-checked:bg-teal-500 transition-all mr-2"></div>
                                        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors">DD-MM-YYYY</span>
                                    </label>
                                    <label class="relative inline-flex items-center cursor-pointer group">
                                        <input type="radio" name="date_format" value="mm-dd-yyyy" class="sr-only peer" {{!$default_setting ? '' : ($default_setting->date_format == 'mm-dd-yyyy' ? 'checked' : '')}}>
                                        <div class="w-4 h-4 rounded-full border-2 border-slate-300 peer-checked:border-teal-500 peer-checked:bg-teal-500 transition-all mr-2"></div>
                                        <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors">MM-DD-YYYY</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delivery Controls Section --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-4 bg-slate-50 border-b">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <i class="hgi hgi-stroke hgi-shield-check text-teal-600"></i> Privacy & Delivery
                        </h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-slate-800 tracking-tight">Obfuscate Images</h4>
                                <p class="text-[10px] text-slate-500 font-medium uppercase">Host image links on Hybrid Mail</p>
                            </div>
                            <div class="flex bg-slate-100 p-1 rounded-xl">
                                <label class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase cursor-pointer transition-all has-[:checked]:bg-white has-[:checked]:text-teal-600 has-[:checked]:shadow-sm">
                                    <input type="radio" name="image_url_hide" value="1" class="hidden" {{!$default_setting ? 'checked' : ($default_setting->image_url_hide == 1 ? 'checked' : '')}}> Yes
                                </label>
                                <label class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase cursor-pointer transition-all has-[:checked]:bg-white has-[:checked]:text-slate-600 has-[:checked]:shadow-sm">
                                    <input type="radio" name="image_url_hide" value="0" class="hidden" {{!$default_setting ? '' : ($default_setting->image_url_hide == 0 ? 'checked' : '')}}> No
                                </label>
                            </div>
                        </div>

                        <div class="h-[1px] bg-slate-50 w-full"></div>

                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <h4 class="text-sm font-bold text-slate-800 tracking-tight">Mute Notifications</h4>
                                <p class="text-[10px] text-slate-500 font-medium uppercase">Silence system activity alerts</p>
                            </div>
                            <div class="flex bg-slate-100 p-1 rounded-xl">
                                <label class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase cursor-pointer transition-all has-[:checked]:bg-white has-[:checked]:text-teal-600 has-[:checked]:shadow-sm">
                                    <input type="radio" name="disable_notification" value="1" class="hidden" {{!$default_setting ? 'checked' : ($default_setting->disable_notification == 1 ? 'checked' : '')}}> Yes
                                </label>
                                <label class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase cursor-pointer transition-all has-[:checked]:bg-white has-[:checked]:text-slate-600 has-[:checked]:shadow-sm">
                                    <input type="radio" name="disable_notification" value="0" class="hidden" {{!$default_setting ? '' : ($default_setting->disable_notification == 0 ? 'checked' : '')}}> No
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Campaign Metadata --}}
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden lg:sticky lg:top-8">
                    <div class="px-8 py-4 bg-slate-50 border-b flex justify-between items-center">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <i class="hgi hgi-stroke hgi-megaphone-03 text-teal-600"></i> Campaign Metadata Defaults
                        </h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">[DEFAULT_FROM_NAME]</label>
                                <input type="text" name="default_from_name" value="{{!$default_setting ? '' : $default_setting->default_from_name}}"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">[DEFAULT_FROM_EMAIL]</label>
                                <input type="email" name="default_from_email" value="{{!$default_setting ? '' : $default_setting->default_from_email}}"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">[DEFAULT_REPLY_TO]</label>
                            <input type="email" name="default_reply_to" value="{{!$default_setting ? '' : $default_setting->default_reply_to}}"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">[DEFAULT_HEADER]</label>
                            <textarea name="default_header" rows="3" 
                                      class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm resize-none">{{!$default_setting ? '' : $default_setting->default_header}}</textarea>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-1 ml-1  ">Use {brackets} for clickable mirror links.</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">[DEFAULT_FOOTER]</label>
                            <textarea name="default_footer" rows="4" 
                                      class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm resize-none">{{!$default_setting ? '' : $default_setting->default_footer}}</textarea>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-1 ml-1 tracking-tight">An unsubscribe link via {brackets} is required.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection