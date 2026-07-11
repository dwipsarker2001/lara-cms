@php $d = $data; @endphp
<section data-block="packageList">
    <div class="max-w-6xl mx-auto px-6">
        <p class="text-sm text-gray-500">Packages will be displayed here (max price: {{ $d['priceMax'] ?? 'unlimited' }}, {{ $d['packagesPerPage'] ?? 5 }} per page).</p>
    </div>
</section>
