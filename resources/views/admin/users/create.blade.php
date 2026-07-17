@extends('admin.layout')

@section('title', 'Add User')
@section('breadcrumb', 'Add User')

@section('content')
    <div class="max-w-3xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <div>
                <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87" />
                        <path d="M16 3.13a4 4 0 010 7.75" />
                    </svg>
                    Add User
                </h1>
                <nav class="flex items-center gap-1.5 text-sm text-text-muted mt-1">
                    <a href="{{ route('admin.users.index') }}" class="hover:text-primary transition-colors no-underline">Users</a>
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-gray-300">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-text-heading font-medium">Add User</span>
                </nav>
            </div>
        </header>

        <form method="POST" action="{{ route('admin.users.store') }}" class="bg-panel-bg rounded-2xl p-2 mb-8">
            @csrf
            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-6 space-y-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-text-heading mb-1.5">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full border border-content-border rounded-lg px-3 py-2 text-sm text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                        placeholder="John Doe">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-text-heading mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full border border-content-border rounded-lg px-3 py-2 text-sm text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                        placeholder="john@example.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Avatar --}}
                <div>
                    <label for="avatar" class="block text-sm font-medium text-text-heading mb-1.5">Avatar URL <span class="text-text-muted font-normal">(optional)</span></label>
                    <input type="url" name="avatar" id="avatar" value="{{ old('avatar') }}"
                        class="w-full border border-content-border rounded-lg px-3 py-2 text-sm text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                        placeholder="https://example.com/avatar.jpg">
                    @error('avatar')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-text-heading mb-1.5">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full border border-content-border rounded-lg px-3 py-2 text-sm text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                        placeholder="Minimum 8 characters">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-text-heading mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full border border-content-border rounded-lg px-3 py-2 text-sm text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary"
                        placeholder="Re-enter password">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4 px-2">
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center justify-center font-medium text-sm px-4 py-2 rounded-lg border border-content-border bg-content-bg text-text-primary hover:bg-body-bg transition-colors no-underline cursor-pointer">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center font-medium text-sm px-4 py-2 rounded-lg bg-primary text-white hover:opacity-90 transition-opacity cursor-pointer">
                    Create User
                </button>
            </div>
        </form>
    </div>
@endsection