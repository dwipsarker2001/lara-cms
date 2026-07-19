@props([
    'headers' => [],
    'items' => [],
    'emptyText' => 'No entries yet.',
    'emptySubtext' => 'Submissions will appear here once the form is submitted.',
])

<div class="overflow-x-auto rounded-xl ring-1 ring-content-border bg-content-bg shadow-sm">
    <table class="w-full border-separate border-spacing-y-0 text-left text-[13px]">
        <thead>
            <tr class="bg-[#f9fafb]">
                @foreach ($headers as $index => $header)
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] {{ $loop->first ? 'rounded-tl-xl' : '' }} {{ $loop->last ? 'rounded-tr-xl text-right' : '' }}">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if ($items->isEmpty())
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-4 py-16 text-center text-text-muted border-b border-content-border bg-white rounded-b-xl">
                        <div class="flex flex-col items-center justify-center">
                            <img src="/empty-collection.svg" alt="No items" class="size-24 mb-3 opacity-60">
                            <p class="text-sm font-medium text-text-heading">{{ $emptyText }}</p>
                            @if ($emptySubtext)
                                <p class="text-xs text-text-muted mt-1">{{ $emptySubtext }}</p>
                            @endif
                        </div>
                    </td>
                </tr>
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
</div>
