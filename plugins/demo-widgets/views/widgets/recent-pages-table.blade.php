<div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-[5px]">
    <div class="overflow-x-auto">
        <table class="w-full border-separate border-spacing-y-0 text-left text-[13px]">
            <thead>
                <tr class="bg-[#f9fafb]">
                    <th class="whitespace-nowrap rounded-l-xl px-4 py-3 font-medium text-text-muted text-[12px]">Title</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">Slug</th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">Status</th>
                    <th class="whitespace-nowrap rounded-r-xl px-4 py-3 font-medium text-text-muted text-[12px]">Updated</th>
                </tr>
                <tr class="h-2"><td colspan="4"></td></tr>
            </thead>
            <tbody>
                @foreach ($pages as $i => $page)
                    <tr class="group transition-colors hover:bg-gray-50/50">
                        <td class="border-b border-gray-100 bg-white px-4 py-3 font-medium text-text-heading {{ $i === 0 ? 'rounded-tl-xl' : '' }} {{ $i === count($pages) - 1 ? 'rounded-bl-xl' : '' }}">
                            {{ $page['title'] }}
                        </td>
                        <td class="border-b border-gray-100 bg-white px-4 py-3 font-mono text-[12px] text-text-muted">
                            {{ $page['slug'] }}
                        </td>
                        <td class="border-b border-gray-100 bg-white px-4 py-3">
                            @if ($page['status'] === 'Published')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-medium text-emerald-700 border border-emerald-200/60">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-medium text-amber-700 border border-amber-200/60">
                                    <span class="size-1.5 rounded-full bg-amber-400"></span>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="border-b border-gray-100 bg-white px-4 py-3 text-text-muted {{ $i === 0 ? 'rounded-tr-xl' : '' }} {{ $i === count($pages) - 1 ? 'rounded-br-xl' : '' }}">
                            {{ $page['updated'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
