<div class="flex items-center justify-between px-4 pb-3">
    <span class="flex items-center gap-2 text-[14px] font-medium text-text-heading">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-text-muted"><circle cx="12" cy="12" r="4" /><path d="M16 8v5a3 3 0 006 0v-1a10 10 0 10-3.92 7.94" /></svg>
        SLA Monitoring
    </span>
    <div class="flex items-center gap-2">
        <div class="relative">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-text-muted"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
            <input type="text" placeholder="Ticket" class="h-8 w-40 rounded-lg border border-content-border bg-white pl-8 pr-3 text-[12px] text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/10 shadow-sm">
        </div>
        <button class="flex h-8 items-center gap-1.5 rounded-lg border border-content-border bg-white px-3 text-[12px] font-medium text-text-heading hover:bg-gray-50 shadow-sm transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 text-text-muted"><polygon points="22 3 22 7 14 15 14 21 10 18 10 15 2 7 2 3 22 3" /></svg>
            Filter
        </button>
        <button class="flex size-8 items-center justify-center rounded-lg border border-content-border bg-white text-text-muted hover:bg-gray-50 shadow-sm transition-colors cursor-pointer">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><circle cx="12" cy="12" r="1" /><circle cx="19" cy="12" r="1" /><circle cx="5" cy="12" r="1" /></svg>
        </button>
    </div>
</div>
<div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-[5px]">
    <div class="overflow-x-auto">
        <table class="w-full border-separate border-spacing-y-0 text-left text-[13px]">
            <thead>
                <tr class="bg-[#f9fafb]">
                    <th class="w-12 rounded-l-xl px-5 py-3"><input type="checkbox" class="size-4 rounded border-gray-300 bg-white text-zinc-900 accent-zinc-900 focus:ring-0 cursor-pointer"></th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                        <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Ticket ID <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                        <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Subject <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                        <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Priority <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                        <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Assigned To <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                        <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Status <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px]">
                        <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">Created Date <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 font-medium text-text-muted text-[12px] rounded-r-xl">
                        <div class="flex items-center gap-1 cursor-pointer hover:text-text-heading">SLA Due <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3 text-gray-300"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" /><path d="M18 2l3 3-9 9-4 1 1-4 9-9z" /></svg></div>
                    </th>
                    <th class="w-4 pr-2"></th>
                </tr>
                <tr class="h-2"><td colspan="9"></td></tr>
            </thead>
            <tbody>
                @foreach ($rows as $i => $row)
                    @php
                        $barsCount = $row->priority === 'High' ? 3 : ($row->priority === 'Medium' ? 2 : 1);
                        $barColor = $row->priority === 'High' ? 'bg-red-500' : ($row->priority === 'Medium' ? 'bg-amber-500' : 'bg-yellow-500');
                    @endphp
                    <tr class="group transition-colors hover:bg-gray-50/50">
                        <td class="border-b border-gray-100 bg-white px-5 py-3 {{ $i === 0 ? 'rounded-tl-xl' : '' }} {{ $i === count($rows) - 1 ? 'rounded-bl-xl' : '' }}">
                            <input type="checkbox" {{ $i === 0 ? 'checked' : '' }} class="size-4 rounded border-gray-300 bg-white text-zinc-900 accent-zinc-900 focus:ring-0 cursor-pointer">
                        </td>
                        <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $row->id }}</td>
                        <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 text-gray-900">{{ $row->subject }}</td>
                        <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 text-gray-900">
                            <span class="inline-flex items-center gap-2 text-gray-900">
                                <span class="inline-flex items-end gap-0.5 h-3.5 mb-0.5">
                                    @foreach ([0, 1, 2] as $b)
                                        <span class="w-0.5 rounded-sm {{ $b < $barsCount ? $barColor : 'bg-gray-200' }}" style="height: {{ [1.5, 2.5, 3.5][$b] * 4 }}px;"></span>
                                    @endforeach
                                </span>
                                <span class="text-[13px]">{{ $row->priority }}</span>
                            </span>
                        </td>
                        <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3">
                            <span class="inline-flex items-center gap-2.5 text-gray-900">
                                @if (isset($agentPhotos[$row->agent]))
                                    <img src="{{ $agentPhotos[$row->agent] }}" alt="{{ $row->agent }}" class="size-6 rounded-full object-cover ring-1 ring-gray-100">
                                @else
                                    <span class="flex size-6 items-center justify-center rounded-full text-[10px] font-medium text-white {{ $avatarColors[$i % count($avatarColors)] }}">{{ strtoupper(substr(implode('', array_map(fn($p) => $p[0] ?? '', explode(' ', $row->agent))), 0, 2)) }}</span>
                                @endif
                                <span class="text-[13px]">{{ $row->agent }}</span>
                            </span>
                        </td>
                        <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3">
                            <span class="inline-flex items-center gap-2.5 text-gray-900">
                                @if ($row->status === 'In Review')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 text-blue-500 shrink-0"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><polyline points="14 2 14 8 20 8" /><line x1="16" y1="13" x2="8" y2="13" /><line x1="16" y1="17" x2="8" y2="17" /></svg>
                                @elseif ($row->status === 'Delivered')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 text-emerald-500 shrink-0"><polyline points="20 6 9 17 4 12" /></svg>
                                @elseif ($row->status === 'In Progress')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 text-amber-500 shrink-0"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                                @endif
                                <span class="text-[13px]">{{ $row->status }}</span>
                            </span>
                        </td>
                        <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 text-gray-400">{{ $row->created }}</td>
                        <td class="border-b border-gray-100 bg-white whitespace-nowrap px-4 py-3 text-gray-900 font-medium">{{ $row->due }}</td>
                        <td class="border-b border-gray-100 bg-white px-4 py-3 pr-5 text-right {{ $i === 0 ? 'rounded-tr-xl' : '' }} {{ $i === count($rows) - 1 ? 'rounded-br-xl' : '' }}">
                            <button class="inline-flex size-7 items-center justify-center rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100/80 transition-colors cursor-pointer">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5"><circle cx="12" cy="12" r="1" /><circle cx="19" cy="12" r="1" /><circle cx="5" cy="12" r="1" /></svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
