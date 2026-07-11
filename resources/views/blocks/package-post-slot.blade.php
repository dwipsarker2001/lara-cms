@php $d = $data; @endphp
<article data-block="packagePostSlot">
    <div class="max-w-4xl mx-auto px-6">
        @if($d['packageListHref'] ?? false)
            <a href="{{ $d['packageListHref'] }}" data-edit="packageListHref" class="inline-flex items-center gap-2 text-sm font-medium text-brand hover:text-brand/80 mb-8">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7 7l-7-7 7-7"/></svg>
                Back to Packages
            </a>
        @endif
    </div>
</article>
