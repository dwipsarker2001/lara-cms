@extends('admin.layout')

@section('title', 'My Preferences')
@section('breadcrumb', 'Settings')

@section('content')
    <div
        class="max-w-5xl mx-auto px-2 sm:px-0"
        style="max-width: 64rem;"
        x-data="{
            language: 'en',
        }"
    >
        <form method="POST" action="{{ route('admin.settings') }}">
            @csrf
            @method('PUT')

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 17 17" class="size-5 text-text-muted shrink-0" fill="none" stroke="currentColor" stroke-width="1">
                        <g transform="translate(1 1)" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5.2598416,5.94378286 C7.1618416,5.94378286 8.23172732,4.87389714 8.23172732,2.97188571 C8.23172732,1.06987429 7.1618416,0 5.2598416,0 C3.35783017,0 2.28794446,1.06987429 2.28794446,2.97188571 C2.28794446,4.87389714 3.35783017,5.94378286 5.2598416,5.94378286 Z" />
                            <path d="M14.3020587,11.5562286 C14.3019445,11.5865143 14.3121159,11.6157714 14.3308587,12.1416 L14.729373,12.1416 C14.8014873,12.2318857 14.8446873,12.3417143 14.853373,12.4569143 C14.8620587,12.5721143 14.835773,12.6873143 14.7779445,13.1917714 C14.4866302,13.2918857 14.4001159,13.3722286 14.2961159,13.4224 C14.1920016,13.4725714 14.0752016,13.4901714 13.9610302,13.4729143 L13.3260587,13.3763429 C13.2960016,13.3721143 13.2654873,13.3779429 13.2390873,13.3929143 L12.4867445,13.8273143 C12.460573,13.8426286 12.4402302,13.8662857 12.4290302,13.8945143 L12.1948587,14.4921143 C12.1529159,14.5995429 12.0795445,14.6918857 11.9843445,14.7569143 C11.8891445,14.8220571 11.776573,14.8569143 11.6612587,14.8571429 L11.1938302,14.8571429 C11.0785159,14.8569143 10.9658302,14.8220571 10.8706302,14.7569143 C10.7754873,14.6918857 10.7021387,14.5995429 10.6601845,14.4921143 L10.4259445,13.8945143 C10.414733,13.8662857 10.3944473,13.8426286 10.3683102,13.8273143 L9.61593303,13.3929143 C9.58956732,13.3779429 9.55898446,13.3721143 9.5289616,13.3763429 L8.89397875,13.4729143 C8.77975017,13.4901714 8.66299589,13.4725714 8.55893875,13.4224 C8.45489303,13.3722286 8.36839017,13.2918857 8.31075589,13.1917714 L8.07703017,12.7873143 C8.01923589,12.6873143 7.9929616,12.5721143 8.00162446,12.4569143 C8.01028732,12.3417143 8.05348732,12.2318857 8.12559017,12.1416 L8.5263216,11.6395429 C8.54507589,11.6157714 8.55523589,11.5865143 8.55514446,11.5562286 L8.55514446,10.6875657 C8.55523589,10.65736 8.54507589,10.6280114 8.5263216,10.60432 L8.12559017,10.1022057 C8.05348732,10.0119657 8.01028732,9.90206857 8.00162446,9.78688 C7.9929616,9.67169143 8.01923589,9.55657143 8.07703017,9.45654857 L8.31075589,9.05208 C8.36839017,8.95197714 8.45489303,8.8716 8.55893875,8.82142857 C8.66299589,8.77125714 8.77975017,8.75364571 8.89397875,8.77088 L9.5273616,8.86746286 C9.55738446,8.87171429 9.58796732,8.86589714 9.61433303,8.85091429 L10.3683102,8.41443429 C10.3944473,8.39904 10.414733,8... (line truncated to 2000 chars)
                            <path d="M11.428573,10.5506399 C11.2841159,10.5505486 11.1395445,10.5926629 11.0387445,10.67696 C10.8137616,10.8650286 10.7995787,11.2948571 10.9865159,11.5145143 C11.0880016,11.6338286 11.2582873,11.6934857 11.428573,11.6933716" />
                            <path d="M11.428573,10.550777 C11.5730302,10.5506971 11.7176016,10.5928 11.8184016,10.6770971 C12.0434302,10.8651429 12.0576016,11.2949714 11.8706302,11.5146286 C11.7691445,11.6339429 11.5988587,11.6936 11.428573,11.6934859" />
                            <path d="M6.14417303,8.21984 C5.85300732,8.18864 5.55730446,8.17264 5.25785303,8.17264 C3.57371589,8.17264 2.00779589,8.67876571 0.703875888,9.5472 C-0.749792683,10.5153943 0.23180046,12.3827429 1.97837875,12.3827429 L5.69643589,12.3827429" />
                        </g>
                    </svg>
                    My Preferences
                </h1>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        Save
                    </button>
                </div>
            </header>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Settings</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Configure your site name and control panel preferences.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">

                            {{-- Language --}}
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading">Language</label>
                                    <div class="text-sm text-text-muted">The preferred language for the control panel.</div>
                                </div>
                                <div>
                                    <div x-data="{ open: false, selected: 'en', options: [{value:'en',label:'English'},{value:'fr',label:'French'},{value:'de',label:'German'},{value:'es',label:'Spanish'},{value:'nl',label:'Dutch'}], get selectedLabel() { return this.options.find(o => o.value === this.selected)?.label ?? 'English' }, select(val) { this.selected = val; this.open = false } }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative">
                                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between bg-content-bg border border-content-border text-text-primary text-sm rounded-lg px-3 py-2 h-10 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                            <span class="truncate" x-text="selectedLabel"></span>
                                            <svg class="size-4 text-text-muted shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <div x-show="open" class="absolute z-50 top-full mt-1 left-0 right-0 bg-content-bg border border-content-border rounded-lg shadow-lg p-1 max-h-60 overflow-y-auto space-y-0.5" style="display: none;">
                                            <template x-for="opt in options" :key="opt.value">
                                                <button type="button" @click="select(opt.value)" class="w-full text-left px-3 py-1.5 text-sm rounded-md transition-colors" :class="opt.value === selected ? 'bg-primary/10 text-primary font-medium' : 'text-text-primary hover:bg-content-border/30'">
                                                    <span x-text="opt.label"></span>
                                                </button>
                                            </template>
                                        </div>
                                        <input type="hidden" name="language" :value="selected">
                                    </div>
                                </div>
                            </div>

                            {{-- Site Title --}}
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-sm font-medium text-text-heading" for="field-site-title">Site Title</label>
                                    <div class="text-sm text-text-muted">The name of your site, shown in the browser tab and control panel.</div>
                                </div>
                                <div>
                                    <input id="field-site-title" type="text" name="site_title" value="{{ old('site_title', $settings->site_title) }}"
                                        class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary" />
                                    @error('site_title') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


        </form>
    </div>
@endsection
