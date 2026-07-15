@extends('admin.layout')

@section('title', 'Posts')
@section('breadcrumb', 'Posts')

@section('content')
    <div class="max-w-5xl mx-auto px-2 sm:px-0">
        <header class="relative flex flex-wrap items-center justify-between gap-4 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <path d="M12 20h9" />
                    <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                </svg>
                Blog Posts
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.posts.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Create Post
                </a>
            </div>
        </header>

        <x-admin::sortable-list
            title="All Posts"
            :items="$posts"
            sortable-id="sortable-posts"
            data-key="postId"
            reorder-route="admin.posts.reorder"
            edit-route="admin.posts.editor"
            update-route="admin.posts.edit"
            delete-route="admin.posts.destroy"
            empty-text="No posts yet."
            empty-link-text="Create your first post"
            empty-link-route="admin.posts.create"
        />
    </div>
@endsection

@push('scripts')
    @if(count($posts) > 1)
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('sortable-posts');
                if (!el || typeof Sortable === 'undefined') return;
                Sortable.create(el, {
                    handle: '[data-drag-handle]',
                    animation: 200,
                    ghostClass: 'opacity-40',
                    dragClass: '!bg-white !shadow-lg !rounded-xl',
                    onEnd() {
                        const ids = Array.from(el.querySelectorAll('[data-post-id]')).map(el => el.dataset.postId);
                        fetch('{{ route('admin.posts.reorder') }}', {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ post_ids: ids }),
                        }).catch(() => {});
                    },
                });
            });
        </script>
    @endif
@endpush
