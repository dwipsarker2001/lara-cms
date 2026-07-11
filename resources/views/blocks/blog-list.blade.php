@php $d = $data; @endphp
<section data-block="blogList">
    <div class="max-w-6xl mx-auto px-6">
        <p class="text-sm text-gray-500">Blog posts will be displayed here ({{ $d['layout'] ?? 'grid' }} layout, {{ $d['postsPerPage'] ?? 6 }} per page).</p>
    </div>
</section>
