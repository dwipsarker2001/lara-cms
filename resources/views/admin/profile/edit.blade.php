@extends('admin.layout')

@section('title', 'Manage Profile')
@section('breadcrumb', 'Manage Profile')

@section('content')
    <div
        class="max-w-5xl mx-auto px-2 sm:px-0"
        x-data="{
            showPassword: {{ $errors->has('password') || $errors->has('password_confirmation') ? 'true' : 'false' }},
            password: '',
            confirmPassword: '',
            avatar: @js(old('avatar', $user->avatar ?? '')),
            altOpen: false,
            alt: '',
            size: null,
            get passwordMismatch() {
                return this.confirmPassword.length > 0 && this.password !== this.confirmPassword;
            },
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
            togglePassword() {
                this.showPassword = !this.showPassword;
                if (!this.showPassword) {
                    this.password = '';
                    this.confirmPassword = '';
                }
            },
            init() {
                if (this.avatar) {
                    this.fetchSize(this.avatar);
                }
            },
        }"
    >
        <form method="POST" action="{{ route('admin.profile.update') }}" @submit="if (showPassword && passwordMismatch) { $event.preventDefault(); window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Passwords do not match.', type: 'error' } })); }">
            @csrf
            @method('PUT')

            <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-5 shrink-0 text-text-muted">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Manage Profile
                </h1>
                <div class="flex items-center gap-2">
                    @if ($errors->any())
                        <span class="text-sm font-medium text-danger" role="alert">{{ $errors->first() }}</span>
                    @endif
                    <button
                        type="button"
                        @click="togglePassword()"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-gradient-to-b from-content-bg to-gray-50 hover:to-gray-100 text-text-primary border border-content-border shadow-sm"
                        x-text="showPassword ? 'Cancel Password' : 'Change Password'"
                    ></button>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm"
                    >
                        Save
                    </button>
                </div>
            </header>

            {{-- Gray panel box (matches pages collection shell) --}}
            <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
                <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Profile Details</div>
                <p class="px-[18px] pb-3 text-sm text-text-muted">Update your name, email, avatar, and password.</p>
                <div class="px-1.5 pb-2">
                    <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-4 sm:px-[18px] py-5 space-y-2">
                    {{-- Name --}}
                    <div class="min-w-0 flex flex-col gap-2">
                        <label class="text-sm font-medium text-text-primary" for="field_name">
                            Name <span class="text-red-600">*</span>
                        </label>
                        <input
                            id="field_name"
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            placeholder="Enter name"
                            required
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                        >
                        @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="min-w-0 flex flex-col gap-2 pt-4">
                        <label class="text-sm font-medium text-text-primary" for="field_email">
                            Email Address <span class="text-red-600">*</span>
                        </label>
                        <input
                            id="field_email"
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            placeholder="Enter email"
                            required
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                        >
                        @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Avatar --}}
                    <div class="min-w-0 flex flex-col gap-2 pt-4">
                        <label class="text-sm font-medium text-text-primary">Avatar</label>
                        <input type="hidden" name="avatar" :value="avatar">
                        <div class="rounded-lg border border-content-border bg-content-bg overflow-hidden shadow-sm">
                            {{-- Toolbar --}}
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
                                        <span x-show="avatar" x-cloak> 1/1 selected</span>
                                    </span>
                                </div>
                            </div>

                            {{-- Selected file row --}}
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
                                        placeholder="Alt text"
                                        class="w-full bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted text-sm rounded-lg px-3 py-1.5 h-9 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                    >
                                </div>
                            </div>
                        </div>
                        @error('avatar') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password fields --}}
                    <template x-if="showPassword">
                        <div>
                            <div class="min-w-0 flex flex-col gap-2 pt-4">
                                <label class="text-sm font-medium text-text-primary" for="field_password">
                                    Password <span class="text-red-600">*</span>
                                </label>
                                <input
                                    id="field_password"
                                    type="password"
                                    name="password"
                                    autocomplete="new-password"
                                    x-model="password"
                                    placeholder="Enter new password"
                                    class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                >
                                @error('password') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="min-w-0 flex flex-col gap-2 pt-4">
                                <label class="text-sm font-medium text-text-primary" for="field_confirm_password">
                                    Confirm Password <span class="text-red-600">*</span>
                                </label>
                                <input
                                    id="field_confirm_password"
                                    type="password"
                                    name="password_confirmation"
                                    autocomplete="new-password"
                                    x-model="confirmPassword"
                                    placeholder="Re-enter new password"
                                    class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                                    :class="passwordMismatch ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20' : ''"
                                >
                                <p class="text-xs text-red-600" x-show="passwordMismatch" x-cloak>Passwords do not match.</p>
                                @error('password_confirmation') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </template>
                </div>
                </div>
            </div>
        </form>
    </div>
@endsection
