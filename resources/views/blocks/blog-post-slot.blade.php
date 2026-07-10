@php $d = $data; @endphp
<article data-block="blogPostSlot">
    <div class="mx-auto max-w-3xl px-4 py-8">
        <a href="{{ $d['blogListUrl'] ?? '/blog' }}" class="mb-8 inline-flex items-center gap-2 text-sm font-medium text-neutral-600 hover:text-primary transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7 7l-7-7 7-7"/></svg>
            Back to Blog
        </a>

        @if($d['image'] ?? false)
            <div data-edit="image" class="mb-8 overflow-hidden rounded-2xl shadow-sm">
                <img src="{{ $d['image'] }}" alt="{{ $d['title'] ?? '' }}" class="w-full object-cover" />
            </div>
        @endif

        <header class="mb-8">
            <h1 data-edit="title" class="text-3xl font-bold tracking-tight text-neutral-900 md:text-4xl lg:text-5xl">{{ $d['title'] ?? '' }}</h1>
            <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-neutral-500">
                @if($d['author'] ?? false)
                    <span data-edit="author" class="flex items-center gap-2">
                        @if($d['authorAvatar'] ?? false)
                            <img src="{{ $d['authorAvatar'] }}" alt="" class="h-6 w-6 rounded-full" />
                        @endif
                        {{ $d['author'] }}
                    </span>
                @endif
                @if($d['date'] ?? false)
                    <time data-edit="date" datetime="{{ $d['date'] }}">{{ $d['date'] }}</time>
                @endif
                @if($d['category'] ?? false)
                    <span data-edit="category" class="rounded-full bg-primary/10 px-3 py-0.5 text-xs font-medium text-primary">{{ $d['category'] }}</span>
                @endif
                @if($d['readTime'] ?? false)
                    <span data-edit="readTime">{{ $d['readTime'] }} min read</span>
                @endif
            </div>
        </header>

        <div data-edit="body" class="prose prose-neutral max-w-none">
            {!! $d['body'] ?? '' !!}
        </div>

        @if(($d['tags'] ?? []))
            <div class="mt-8 flex flex-wrap gap-2 border-t border-neutral-100 pt-6">
                @foreach($d['tags'] as $tag)
                    <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-600">{{ $tag }}</span>
                @endforeach
            </div>
        @endif

        @if(($d['shareLinks'] ?? []))
            <div class="mt-6 flex items-center gap-3">
                <span class="text-sm font-medium text-neutral-500">Share:</span>
                <div class="flex gap-2">
                    @foreach($d['shareLinks'] as $link)
                        <a href="{{ $link['url'] ?? '#' }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-neutral-100 text-neutral-600 hover:bg-primary hover:text-white transition">
                            @if($link['icon'] ?? false)
                                <img src="{{ $link['icon'] }}" alt="" class="h-4 w-4" />
                            @else
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z"/></svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</article>
