@extends('admin.layout')

@section('title', 'Users')
@section('breadcrumb', 'Users')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 00-3-3.87" />
                    <path d="M16 3.13a4 4 0 010 7.75" />
                </svg>
                Users
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add User
                </a>
            </div>
        </header>

        <div class="bg-panel-bg rounded-2xl mb-8 p-2">
            <div class="flex items-center justify-between px-2 pb-2.5">
                <span class="flex items-center gap-2 text-[14px] font-medium text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 00-3-3.87" />
                        <path d="M16 3.13a4 4 0 010 7.75" />
                    </svg>
                    All Users
                </span>
            </div>

            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm">
                @if ($users->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 rounded-xl">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="size-12 text-text-muted/40 mb-3">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                        <p class="text-sm font-medium text-text-heading">No users yet</p>
                        <p class="text-sm text-text-muted mt-1">
                            <a href="{{ route('admin.users.create') }}" class="text-primary hover:text-primary/80 no-underline font-medium">Add your first user</a>
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-0 text-left text-[13px]">
                            <thead>
                                <tr class="bg-[#f9fafb]">
                                    <th class="rounded-l-xl px-5 py-3 font-medium text-text-muted text-[12px]">Name</th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">Email</th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">Avatar</th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">Created</th>
                                    <th class="rounded-r-xl pr-2"></th>
                                </tr>
                                <tr class="h-2">
                                    <td colspan="5"></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    <tr class="group transition-colors hover:bg-gray-50/50">
                                        <td class="border-b border-gray-100 bg-content-bg px-5 py-3"
                                            @class([
                                                'rounded-tl-xl' => $index === 0,
                                                'rounded-bl-xl' => $index === $users->count() - 1,
                                            ])>
                                            <div class="flex items-center gap-3">
                                                <div class="size-8 rounded-lg overflow-hidden bg-gray-100 shrink-0 flex items-center justify-center">
                                                    @if ($user->avatar)
                                                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="size-full object-cover">
                                                    @else
                                                        <span class="text-xs font-medium text-primary">
                                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-text-heading font-medium">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-text-heading">{{ $user->email }}</td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-text-muted text-[12px]">
                                            {{ $user->avatar ? 'Set' : '—' }}
                                        </td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-text-heading">{{ $user->created_at->diffForHumans() }}</td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-right"
                                            @class([
                                                'rounded-tr-xl' => $index === 0,
                                                'rounded-br-xl' => $index === $users->count() - 1,
                                            ])>
                                            <div class="inline-flex items-center gap-1">
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                    class="inline-flex items-center justify-center size-7 rounded-md text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5">
                                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                    </svg>
                                                </a>
                                                @if ($user->id !== auth()->id())
                                                    <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                                                        <button
                                                            type="button"
                                                            @click="open = !open"
                                                            class="inline-flex items-center justify-center size-7 rounded-md text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors"
                                                        >
                                                            <svg viewBox="0 0 16 3" class="size-3.5" fill="currentColor">
                                                                <circle cx="2" cy="1.5" r="1.5" />
                                                                <circle cx="8" cy="1.5" r="1.5" />
                                                                <circle cx="14" cy="1.5" r="1.5" />
                                                            </svg>
                                                        </button>
                                                        <div
                                                            x-show="open"
                                                            x-transition:enter="transition ease-out duration-100"
                                                            x-transition:enter-start="opacity-0 scale-95"
                                                            x-transition:enter-end="opacity-100 scale-100"
                                                            x-transition:leave="transition ease-in duration-75"
                                                            x-transition:leave-start="opacity-100 scale-100"
                                                            x-transition:leave-end="opacity-0 scale-95"
                                                            class="absolute right-0 top-full mt-1 min-w-[12rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 z-50"
                                                        >
                                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-emerald-600 hover:bg-emerald-50"
                                                            >
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-emerald-500">
                                                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                                </svg>
                                                                <span>Edit</span>
                                                            </a>
                                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="w-full">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                    onclick="return confirm('Are you sure you want to delete this user?')"
                                                                    class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-red-600 hover:bg-red-50 cursor-pointer"
                                                                >
                                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-red-500">
                                                                        <polyline points="3 6 5 6 21 6" />
                                                                        <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                                                                    </svg>
                                                                    <span>Delete</span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            @if ($users->isNotEmpty())
                <footer class="flex justify-between flex-wrap items-center px-[18px] pt-2.5 md:pt-3 pb-2.5 antialiased">
                    <div class="text-sm text-text-muted">
                        {{ $users->count() }} user{{ $users->count() !== 1 ? 's' : '' }}
                    </div>
                </footer>
            @endif
        </div>
    </div>
@endsection