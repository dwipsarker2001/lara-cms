@extends('admin.layout')

@section('title', 'Administrators')

@section('content')
<div x-data="adminPage()">
    <div class="max-w-5xl mx-auto">

        {{-- PageHeader --}}
        <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                Administrators
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.administrators.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add Administrator
                </a>
            </div>
        </header>

        {{-- DataTable --}}
        <div class="bg-panel-bg rounded-2xl mb-8 p-2">
            <div class="flex items-center justify-between px-2 pb-2.5">
                <span class="flex items-center gap-2 text-[14px] font-medium text-text-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                    All Administrators
                </span>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-text-muted">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Search administrators..."
                            class="h-8 w-40 rounded-lg border border-content-border bg-content-bg pl-8 pr-3 text-[12px] text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/10 shadow-sm"
                        >
                    </div>
                    <button type="button"
                        class="flex h-8 items-center gap-1.5 rounded-lg border border-content-border bg-content-bg px-3 text-[12px] font-medium text-text-heading hover:bg-body-bg shadow-sm transition-colors cursor-pointer">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-text-muted">
                            <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                        </svg>
                        Filter
                    </button>
                    <button type="button"
                        class="flex size-8 items-center justify-center rounded-lg border border-content-border bg-content-bg text-text-muted hover:bg-body-bg shadow-sm transition-colors cursor-pointer">
                        <svg viewBox="0 0 16 3" class="size-4" fill="currentColor">
                            <circle cx="2" cy="1.5" r="1.5" />
                            <circle cx="8" cy="1.5" r="1.5" />
                            <circle cx="14" cy="1.5" r="1.5" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm p-[5px]">
                @if ($admins->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="size-12 text-text-muted/40 mb-3">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        <p class="text-sm font-medium text-text-heading">No administrators yet</p>
                        <p class="text-sm text-text-muted mt-1">
                            <a href="{{ route('admin.administrators.create') }}" class="text-primary hover:text-primary/80 no-underline font-medium">Add your first administrator</a>
                        </p>
                    </div>
                @else
                    <template x-if="search && filteredCount === 0">
                        <div class="flex flex-col items-center justify-center py-16">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="size-12 text-text-muted/40 mb-3">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            <p class="text-sm font-medium text-text-heading">No administrators match your search</p>
                        </div>
                    </template>
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-0 text-left text-[13px]">
                            <thead>
                                <tr class="bg-[#f9fafb]">
                                    <th class="rounded-l-xl px-5 py-3 font-medium text-text-muted text-[12px]">Name</th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">Email</th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">Avatar</th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">Status</th>
                                    <th class="px-4 py-3 font-medium text-text-muted text-[12px]">Created</th>
                                    <th class="rounded-r-xl pr-2"></th>
                                </tr>
                                <tr class="h-2">
                                    <td colspan="6"></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admins as $index => $admin)
                                    <tr x-show="matchesSearch({{ json_encode($admin->name) }}, {{ json_encode($admin->email) }})"
                                        class="group transition-colors hover:bg-gray-50/50">
                                        <td class="border-b border-gray-100 bg-content-bg px-5 py-3"
                                            @class([
                                                'rounded-tl-xl' => $index === 0,
                                                'rounded-bl-xl' => $index === $admins->count() - 1,
                                            ])>
                                            <div class="flex items-center gap-3">
                                                <div class="size-8 rounded-lg overflow-hidden bg-gray-100 shrink-0 flex items-center justify-center">
                                                    @if ($admin->avatar)
                                                        <img src="{{ $admin->avatar }}" alt="{{ $admin->name }}" class="size-full object-cover">
                                                    @else
                                                        <span class="text-xs font-medium text-primary">
                                                            {{ strtoupper(substr($admin->name, 0, 2)) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-text-heading font-medium">{{ $admin->name }}</span>
                                            </div>
                                        </td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-text-heading">{{ $admin->email }}</td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-text-muted text-[12px]">
                                            {{ $admin->avatar ? 'Set' : '—' }}
                                        </td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3">
                                            @if ($admin->is_active)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/10">
                                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500 ring-1 ring-gray-200">
                                                    <span class="size-1.5 rounded-full bg-gray-400"></span>
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-text-heading">{{ $admin->created_at->diffForHumans() }}</td>
                                        <td class="border-b border-gray-100 bg-content-bg px-4 py-3 text-right"
                                            @class([
                                                'rounded-tr-xl' => $index === 0,
                                                'rounded-br-xl' => $index === $admins->count() - 1,
                                            ])>
                                            <div class="relative"
                                                 x-data="{ open: false }"
                                                 @click.outside="open = false"
                                                 @keydown.escape.window="open = false">
                                                <button type="button"
                                                    @click="open = !open"
                                                    class="inline-flex size-7 items-center justify-center rounded-md text-text-muted hover:text-text-heading hover:bg-body-bg transition-colors cursor-pointer">
                                                    <svg viewBox="0 0 16 3" class="size-4" fill="currentColor">
                                                        <circle cx="2" cy="1.5" r="1.5" />
                                                        <circle cx="8" cy="1.5" r="1.5" />
                                                        <circle cx="14" cy="1.5" r="1.5" />
                                                    </svg>
                                                </button>
                                                <div x-show="open"
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="absolute right-0 top-full mt-1 min-w-[12rem] rounded-xl border border-content-border bg-content-bg shadow-xl p-1.5 z-50">
                                                    <a href="{{ route('admin.administrators.edit', $admin) }}"
                                                        class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm no-underline transition-colors text-text-primary hover:bg-body-bg cursor-pointer">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-text-muted">
                                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                        </svg>
                                                        <span>Edit</span>
                                                    </a>
                                                    @if ($admin->id !== auth('admin')->id())
                                                        <form method="POST" action="{{ route('admin.administrators.destroy', $admin) }}" class="w-full">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                onclick="return confirm('Are you sure you want to delete this administrator?')"
                                                                class="flex w-full items-center justify-start gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-red-600 hover:bg-red-50 cursor-pointer">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 shrink-0 text-red-500">
                                                                    <polyline points="3 6 5 6 21 6" />
                                                                    <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" />
                                                                </svg>
                                                                <span>Delete</span>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <footer class="flex justify-between flex-wrap items-center px-[18px] pt-2.5 md:pt-3 pb-2.5 antialiased">
                <div class="text-sm text-text-muted">
                    <span x-text="`${filteredCount} administrator${filteredCount !== 1 ? 's' : ''}`"></span>
                </div>
            </footer>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function adminPage() {
        return {
            search: '',
            matchesSearch(name, email) {
                if (!this.search.trim()) return true;
                const q = this.search.toLowerCase();
                return name.toLowerCase().includes(q) || email.toLowerCase().includes(q);
            },
            get filteredCount() {
                if (!this.search.trim()) return {{ $admins->count() }};
                const q = this.search.toLowerCase();
                let count = 0;
                @foreach ($admins as $admin)
                    if ({{ Js::from($admin->name) }}.toLowerCase().includes(q) || {{ Js::from($admin->email) }}.toLowerCase().includes(q)) count++;
                @endforeach
                return count;
            },
        };
    }
</script>
@endpush
@endsection
