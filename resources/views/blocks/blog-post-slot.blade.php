@php $d = $data; @endphp
<article data-block="blogPostSlot">
    <div class="max-w-3xl mx-auto px-6">
        @if($d['blogListHref'] ?? false)
            <a href="{{ $d['blogListHref'] }}" data-edit="blogListHref" class="inline-flex items-center gap-2 text-sm font-medium text-brand hover:text-brand/80 mb-8">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7 7l-7-7 7-7"/></svg>
                Back to Blog
            </a>
        @endif
    </div>
</article>
