@extends('admin.layout')

@section('title', 'New Package')
@section('breadcrumb', 'New Package')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0">
    <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
        <h1 class="flex items-center gap-2.5 text-[25px] leading-[1.25] font-medium text-text-heading">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                <path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16" />
            </svg>
            New Package
        </h1>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.subscription-plans.index') }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200"
            >Cancel</a>
            <button type="submit" form="plan-form"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
            >Create Package</button>
        </div>
    </header>

    <form id="plan-form" method="POST" action="{{ route('admin.subscription-plans.store') }}">
        @csrf

        <div class="space-y-6">
            <div class="bg-panel-bg rounded-2xl p-[7px]">
                <div class="px-[18px] pt-3 pb-1">
                    <div class="text-sm font-medium text-text-heading">Package Details</div>
                </div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Configure the package name and usage limits for this plan.</p>
                <div class="px-1.5 pb-2 space-y-4">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-3 py-3">
                        <div class="divide-y divide-content-border">
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="name" class="text-sm font-medium text-text-heading">Package Name</label>
                                    <div class="text-sm text-text-muted">The name of this subscription package (e.g. Free Trial, Standard, Premium).</div>
                                </div>
                                <div>
                                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Free Trial" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="price" class="text-sm font-medium text-text-heading">Price</label>
                                    <div class="text-sm text-text-muted">The price of this subscription package.</div>
                                </div>
                                <div>
                                    <input id="price" type="number" step="0.01" name="price" value="{{ old('price', 0) }}" min="0" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="max_emails" class="text-sm font-medium text-text-heading">Max Emails</label>
                                    <div class="text-sm text-text-muted">Maximum number of emails allowed for this package.</div>
                                </div>
                                <div>
                                    <input id="max_emails" type="number" name="max_emails" value="{{ old('max_emails', 0) }}" min="0" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    @error('max_emails') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="max_contacts" class="text-sm font-medium text-text-heading">Max Contacts</label>
                                    <div class="text-sm text-text-muted">Maximum number of contacts allowed for this package.</div>
                                </div>
                                <div>
                                    <input id="max_contacts" type="number" name="max_contacts" value="{{ old('max_contacts', 0) }}" min="0" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    @error('max_contacts') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="max_campaigns" class="text-sm font-medium text-text-heading">Max Campaigns</label>
                                    <div class="text-sm text-text-muted">Maximum number of campaigns allowed for this package.</div>
                                </div>
                                <div>
                                    <input id="max_campaigns" type="number" name="max_campaigns" value="{{ old('max_campaigns', 0) }}" min="0" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    @error('max_campaigns') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <label for="max_groups" class="text-sm font-medium text-text-heading">Max Groups</label>
                                    <div class="text-sm text-text-muted">Maximum number of groups allowed for this package.</div>
                                </div>
                                <div>
                                    <input id="max_groups" type="number" name="max_groups" value="{{ old('max_groups', 0) }}" min="0" class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-2 h-9 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                    @error('max_groups') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 items-start px-[18px] py-4 gap-y-3 md:gap-y-0 md:gap-x-5">
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-sm font-medium text-text-heading">Set as Default</span>
                                    <div class="text-sm text-text-muted">New users will automatically get this package on registration.</div>
                                </div>
                                <div class="flex justify-end"><label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="set_as_default" value="1" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                </label></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
