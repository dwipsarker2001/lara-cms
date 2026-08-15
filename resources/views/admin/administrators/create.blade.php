@extends('admin.layout')

@section('title', 'Add Administrator')

@section('content')
    <div
        class="max-w-5xl mx-auto px-2 sm:px-0"
        x-data="{
            avatar: @js(old('avatar')),
            altOpen: false,
            alt: '',
            size: null,
            get avatarName() {
                if (!this.avatar) return '';
                try {
                    const path = this.avatar.split('?')[0];
                    return decodeURIComponent(path.split('/').pop() || path);
                } catch (e) {
                    return this.avatar;
                }
            },
            formatSize(bytes) {
                if (bytes == null) return '';
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
            },
            openAssetPicker() {
                window.dispatchEvent(new CustomEvent('open-asset-picker', {
                    detail: {
                        callback: (url) => {
                            this.avatar = url;
                            this.fetchSize(url);
                        }
                    }
                }));
            },
            clearAvatar() {
                this.avatar = '';
                this.alt = '';
                this.altOpen = false;
                this.size = null;
            },
            fetchSize(url) {
                if (!url) {
                    this.size = null;
                    return;
                }
                fetch(url, { method: 'HEAD' })
                    .then((r) => {
                        const len = r.headers.get('content-length');
                        this.size = len ? parseInt(len, 10) : null;
                    })
                    .catch(() => { this.size = null; });
            },
            isActive: true,
            password: '',
            generatePassword() {
                const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
                let pwd = '';
                for (let i = 0; i < 16; i++) {
                    pwd += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                this.password = pwd;
            },
            init() {
                if (this.avatar) {
                    this.fetchSize(this.avatar);
                }
            },
        }"
    >
        <form method="POST" action="{{ route('admin.administrators.store') }}">
            @csrf

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <div>
                    <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-6 shrink-0 text-text-muted">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        Add Administrator
                    </h1>
                </div>
                <div class="flex items-center gap-3">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        Create Administrator
                    </button>
                </div>
            </header>

            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Administrator Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Enter the details for the new administrator.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-4 sm:px-[18px] py-5 space-y-2">
                        <div class="min-w-0 flex flex-col gap-2">
                            <label class="text-sm font-medium text-text-primary" for="name">
                                Name <span class="text-red-600">*</span>
                            </label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                placeholder="Enter name"
                                class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            >
                            @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="min-w-0 flex flex-col gap-2 pt-4">
                            <label class="text-sm font-medium text-text-primary" for="email">
                                Email Address <span class="text-red-600">*</span>
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="Enter email"
                                class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                            >
                            @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="min-w-0 flex flex-col gap-2 pt-4">
                            <label class="text-sm font-medium text-text-primary">Avatar</label>
                            <input type="hidden" name="avatar" :value="avatar">
                            <div class="rounded-lg border border-content-border bg-content-bg overflow-hidden shadow-sm">
                                <div class="flex items-center gap-3 px-2.5 py-2.5">
                                    <button
                                        type="button"
                                        @click="openAssetPicker()"
                                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-8 text-xs leading-tight px-3 bg-gradient-to-b from-content-bg to-gray-50 hover:to-gray-100 text-text-primary border border-content-border shadow-sm"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" class="size-4 shrink-0">
                                            <path d="M3 7a2 2 0 0 1 2-2h3.5l2 2H19a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                        </svg>
                                        Browse Assets
                                    </button>
                                    <div class="flex items-center gap-1.5 text-sm text-text-muted min-w-0">
                                        <svg viewBox="0 0 24 24" fill="none" class="size-4 shrink-0">
                                            <path d="M7 18a4 4 0 0 1-.5-7.97A5 5 0 0 1 16 8.5a3.5 3.5 0 0 1 1.5 6.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M12 11.5v6m0-6 2 2m-2-2-2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span class="truncate">
                                            Drag &amp; drop here or
                                            <button type="button" @click="openAssetPicker()" class="underline hover:text-text-primary">choose a file</button>.
                                        </span>
                                    </div>
                                </div>

                                <div class="border-t border-content-border" x-show="avatar" x-cloak>
                                    <div class="flex items-center gap-3 px-2.5 py-2">
                                        <div class="size-8 rounded-md overflow-hidden bg-panel-bg flex items-center justify-center shrink-0">
                                            <img :src="avatar" :alt="alt || avatarName" class="size-full object-cover">
                                        </div>
                                        <span class="flex-1 min-w-0 truncate text-sm text-text-primary" x-text="avatarName"></span>
                                        <button
                                            type="button"
                                            @click="altOpen = !altOpen"
                                            class="shrink-0 rounded-md border px-2 py-0.5 text-xs font-medium transition-colors"
                                            :class="alt
                                                ? 'border-primary bg-primary/10 text-primary'
                                                : 'border-content-border text-primary hover:bg-primary/10'"
                                        >
                                            Set Alt
                                        </button>
                                        <span class="shrink-0 text-xs text-text-muted tabular-nums" x-show="size != null" x-text="formatSize(size)"></span>
                                        <button
                                            type="button"
                                            aria-label="Remove avatar"
                                            @click="clearAvatar()"
                                            class="shrink-0 flex size-6 items-center justify-center rounded-md text-text-muted hover:bg-text-primary/10 hover:text-text-primary transition-colors"
                                        >
                                            <svg viewBox="0 0 24 24" fill="none" class="size-4">
                                                <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="px-2.5 pb-2.5" x-show="altOpen" x-cloak>
                                        <input
                                            type="text"
                                            x-model="alt"
                                            name="alt"
                                            placeholder="Alt text"
                                            class="w-full bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-1.5 h-9 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                        >
                                    </div>
                                </div>
                            </div>
                            @error('avatar') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="min-w-0 flex flex-col gap-2 pt-4">
                            <label class="text-sm font-medium text-text-primary" for="password">
                                Password <span class="text-red-600">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input
                                    id="password"
                                    type="text"
                                    name="password"
                                    x-model="password"
                                    required
                                    placeholder="Enter or generate a password"
                                    class="flex-1 block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                >
                                <button
                                    type="button"
                                    @click="generatePassword()"
                                    class="shrink-0 inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-gradient-to-b from-content-bg to-gray-50 hover:to-gray-100 text-text-primary border border-content-border shadow-sm"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                        <path d="M7 11V7a5 5 0 0110 0v4" />
                                    </svg>
                                    Generate
                                </button>
                            </div>
                            @error('password') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid md:grid-cols-2 items-start py-3 gap-y-3 md:gap-y-0 md:gap-x-5">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-sm font-medium text-text-heading">Active</label>
                                <div class="text-sm text-text-muted">Allow this administrator to access the admin panel.</div>
                            </div>
                            <div class="flex items-center h-full">
                                <label class="flex items-center cursor-pointer select-none">
                                    <button type="button" role="switch" :aria-checked="isActive" :data-state="isActive ? 'checked' : 'unchecked'" @click="isActive = !isActive" class="relative flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/30 data-[state=checked]:shadow-inner data-[state=checked]:border-emerald-500 data-[state=checked]:bg-emerald-500 data-[state=unchecked]:!border-gray-300 data-[state=unchecked]:bg-gray-200">
                                        <span :data-state="isActive ? 'checked' : 'unchecked'" class="my-auto flex items-center justify-center size-5 rounded-full bg-white text-xs shadow-[0_10px_15px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] transition-transform will-change-transform data-[state=checked]:translate-x-full data-[state=unchecked]:translate-x-0"></span>
                                    </button>
                                    <input type="hidden" name="is_active" :value="isActive ? '1' : '0'">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
