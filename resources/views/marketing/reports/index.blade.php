@extends('marketing.layouts.app')

@section('content')
<div class="w-full min-h-screen bg-slate-50/50 p-6 text-slate-900">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Campaign Reports</h1>
                <p class="text-slate-500 text-sm mt-2">Deep dive into your email performance and engagement metrics.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="openTools()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-bold shadow-sm hover:bg-slate-50 transition-all">
                    <i class="hgi hgi-stroke hgi-analytics-01 text-teal-600"></i> Analytics Tools
                </button>
                <button type="button" onclick="printContent()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-bold shadow-sm hover:bg-slate-50 transition-all">
                    <i class="hgi hgi-stroke hgi-printer text-slate-500"></i> Print
                </button>
                <button onclick="downloadContent()" class="flex items-center gap-3 px-6 py-2 rounded-lg text-white 
                        bg-gradient-to-br from-[#007682] to-[#408b86]
                        hover:brightness-110
                        transition-all duration-300 active:scale-95">
                    <i class="hgi hgi-stroke hgi-download-04"></i>
                    <span class="font-bold text-sm">Export PNG</span>
                </button>
            </div>
        </div>

        <div id="contentToDownload" class="">
            
            {{-- 1. Top Row: 4 Metric Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                {{-- Open Rate Card --}}                
                <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
                    <div>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Open Rate</p>
                        <p class="text-2xl font-semibold text-slate-900">{{$allSent!=0?round($allOpened/$allSent*100):0}}%</p>
                    </div>
                    <div class="h-10 w-10 bg-green-50 rounded-full grid place-content-center text-slate-400 border border-slate-100 group-hover:bg-green-100 transition-colors">
                        <i class="hgi hgi-stroke hgi-mail-open-01 text-green-600 text-lg"></i>
                    </div>
                </div>

                {{-- Click Rate Card --}}
                <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
                    <div>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Click Rate</p>
                        <p class="text-2xl font-semibold text-slate-900">{{$allSent!=0?round($allClicked/$allSent*100):0}}%</p>
                    </div>
                    <div class="h-10 w-10 bg-indigo-50 rounded-full grid place-content-center text-slate-400 border border-slate-100 group-hover:bg-indigo-100 transition-colors">
                        <i class="hgi hgi-stroke hgi-cursor-pointer-01 text-indigo-600 text-lg"></i>
                    </div>
                </div>

                {{-- Unsubscribe Rate Card --}}
                <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
                    <div>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Unsubscribe Rate</p>
                        <p class="text-2xl font-semibold text-slate-900">{{$allSent!=0?round($allUnsubscribed/$allSent*100):0}}%</p>
                    </div>
                    <div class="h-10 w-10 bg-red-50 rounded-full grid place-content-center text-slate-400 border border-slate-100 group-hover:bg-red-100 transition-colors">
                        <i class="hgi hgi-stroke hgi-user-block-02 text-lg text-red-600"></i>
                    </div>
                </div>

                {{-- Bounce Rate Card --}}
                <div class="bg-white p-6 rounded-lg border border-slate-200 flex items-center justify-between group hover:border-slate-300 transition-colors shadow-sm">
                    <div>
                        <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Bounce Rat</p>
                        <p class="text-2xl font-semibold text-slate-900">{{$allSent!=0?round($allBounced/$allSent*100):0}}%</p>
                    </div>
                    <div class="h-10 w-10 bg-red-50 rounded-full grid place-content-center text-slate-400 border border-slate-100 group-hover:bg-slate-100 transition-colors">
                        <i class="hgi hgi-stroke hgi-mail-remove-02 text-lg text-red-600"></i>
                    </div>
                </div>
            </div>

            {{-- 2. Middle Row: Chart and Summary List --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Left Side: Chart --}}
                <div class="lg:col-span-2 bg-white p-6 rounded-lg border border-slate-200 shadow-sm">
                    <h4 class="text-sm font-bold text-slate-700 uppercase mb-8 tracking-tight">Engagement Trend</h4>
                    <div id="mainChartContainer" style="height: 200px; width: 100%;"></div>
                </div>

                {{-- Right Side: Total Summary List --}}
                <div class="bg-gradient-to-br from-[#007682] to-[#408b86] p-8 rounded-lg shadow-lg flex flex-col justify-center">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center border-b border-white/10">
                            <span class="text-white/80 text-xs font-bold uppercase">Total Sent</span>
                            <span class="text-white">{{$allSent}}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/10">
                            <span class="text-white/80 text-xs font-bold uppercase">Recipients</span>
                            <span class="text-white">{{$allRecipient}}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/10">
                            <span class="text-white/80 text-xs font-bold uppercase">Bounced</span>
                            <span class="text-rose-300">{{$allBounced}}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/10">
                            <span class="text-white/80 text-xs font-bold uppercase">Unsubscribed</span>
                            <span class="text-amber-300">{{$allUnsubscribed}}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-white/80 text-xs font-bold uppercase">Total Opened</span>
                            <span class="text-teal-200">{{$allOpened}}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/10">
                            <span class="text-white/80 text-xs font-bold uppercase">Unsubscribed</span>
                            <span class="text-amber-300">{{$allUnsubscribed}}</span>
                        </div>
                                                <div class="flex justify-between items-center border-b border-white/10">
                            <span class="text-white/80 text-xs font-bold uppercase">Unsubscribed</span>
                            <span class="text-amber-300">{{$allUnsubscribed}}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Shadcn-style Filter Bar & Header --}}
            <div class="bg-white rounded-t-xl border-x border-t border-slate-200 py-4 px-8">
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6">
                    
                    {{-- Title Section --}}
                    <div class="">
                        <h2 class="text-lg font-semibold text-slate-900 tracking-tight mb-0.5">Campaign Performance</h2>
                        <p class="text-sm text-slate-500">Review and manage your email performance results.</p>
                    </div>

                    <div>
                        <form action="{{route('app.report.filter')}}" method="POST" class="flex flex-wrap items-center justify-end gap-3">
                            @csrf
                            
                            {{-- Integrated Date Range Input (Shadcn Input Style) --}}
                            <div class="flex items-center bg-white border border-slate-200 rounded-md shadow-sm h-10 px-3 py-1 focus-within:ring-2 focus-within:ring-slate-950 focus-within:ring-offset-2 transition-all">
                                <div class="flex items-center">
                                    <span class="text-[11px] font-medium text-slate-400 uppercase mr-2">From</span>
                                    <input type="date" name="from" value="{{$from}}" required 
                                        class="border-none p-0 text-sm font-medium focus:ring-0 bg-transparent w-32 cursor-pointer">
                                </div>
                                <div class="h-4 w-[1px] bg-slate-200 mx-3"></div>
                                <div class="flex items-center">
                                    <span class="text-[11px] font-medium text-slate-400 uppercase mr-2">To</span>
                                    <input type="date" name="to" value="{{$to}}" required 
                                        class="border-none p-0 text-sm font-medium focus:ring-0 bg-transparent w-32 cursor-pointer">
                                </div>
                            </div>

                            {{-- Primary Action: Show Statistics (Shadcn "Primary") --}}
                            <button type="submit" class="inline-flex items-center bg-gradient-to-br from-[#007682] to-[#408b86] justify-center rounded-md text-sm font-medium transition-colors bg-slate-900 text-slate-50 hover:bg-slate-900/90 h-10 px-4 py-2 shadow active:scale-[0.98]">
                                <i class="hgi hgi-stroke hgi-analytics-01 mr-2 text-lg"></i>
                                Show Statistics
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- 4. Beautiful Table --}}
            <div class="bg-white rounded-b-xl border-x border-b border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="border-b border-slate-400 bg-gradient-to-r from-[#f0f9f9] via-[#e6f2f2] to-[#d1e6e6]">
                            <tr class="bg-slate-50/80 border-y border-slate-100">
                                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Report ID</th>
                                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Audience Size</th>
                                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Click Rate</th>
                                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">Performance Metrics</th>
                                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sent Date</th>
                                <th class="px-6 py-3.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($stats as $stat)
                            @php
                                $sentCount     = $stat->total_sent != NULL ? count(explode(',', $stat->total_sent)) : 0;
                                $clickedCount  = $stat->clicked != NULL ? count(explode(',', $stat->clicked)) : 0;
                                $bouncedCount  = $stat->bounced != NULL ? count(explode(',', $stat->bounced)) : 0;
                                $unsubCount    = $stat->black_list != NULL ? count(explode(',', $stat->black_list)) : 0;
                                
                                $clickRate = $sentCount > 0 ? round(($clickedCount / $sentCount) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                {{-- ID Column --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <i class="hgi hgi-stroke hgi-megaphone-03 text-[#1f8084] text-lg ml-4"></i>
                                        <!-- <span class="text-xs font-mono font-bold text-slate-400">#{{$stat->id}}</span> -->
                                    </div>
                                </td>

                                {{-- Audience Column --}}
                                <td class="px-6 py-5">
                                    <div class="flex flex-col text-center">
                                        <span class="text-sm font-bold text-slate-600 mb-1">{{ number_format($sentCount) }}</span>
                                        <span class="text-[11px] text-slate-500 font-medium uppercase">Recipients</span>
                                    </div>
                                </td>

                                {{-- Click Rate Percentage --}}
                                <td class="px-6 py-5 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="text-sm font-semibold text-slate-700 mb-1">{{ $clickRate }}%</span>
                                        <span class="text-[9px] font-bold text-teal-600 uppercase">Avg. CTR</span>
                                    </div>
                                </td>

                                {{-- Performance Metrics Cluster (Matching your reference) --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-6">
                                        <div class="text-center">
                                            <p class="text-sm font-semibold text-indigo-600 mb-0.5">{{ number_format($clickedCount) }}</p>
                                            <p class="text-[11px] text-slate-500 font-medium uppercase">Clicks</p>
                                        </div>
                                        <div class="text-center border-x border-slate-100 px-6">
                                            <p class="text-sm font-semibold text-rose-600 mb-0.5">{{ number_format($bouncedCount) }}</p>
                                            <p class="text-[11px] text-slate-500 font-medium uppercase">Bounces</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-sm font-semibold text-amber-600 mb-0.5">{{ number_format($unsubCount) }}</p>
                                            <p class="text-[11px] text-slate-500 font-medium uppercase">Unsubs</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Date Column --}}
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-600 mb-1">{{ \Carbon\Carbon::parse($stat->created_at)->format('M d, Y') }}</span>
                                        <span class="text-[11px] text-slate-500 font-medium">{{ \Carbon\Carbon::parse($stat->created_at)->format('h:i A') }}</span>
                                    </div>
                                </td>

                                {{-- Actions Column --}}
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="#" class="h-9 w-9 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-900 hover:bg-slate-100 border border-transparent transition-all">
                                            <i class="hgi hgi-stroke hgi-view text-lg"></i>
                                        </a>
                                        <a onclick="return confirm('Archive this report?')" href="{{route('app.report.delete', $stat->id)}}" 
                                            class="h-9 w-9 flex items-center justify-center rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all">
                                            <i class="hgi hgi-stroke hgi-delete-02 text-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Optional Empty State --}}
                @if(count($stats) == 0)
                <div class="p-12 text-center">
                    <i class="hgi hgi-stroke hgi-folder-open text-4xl text-slate-200"></i>
                    <p class="mt-4 text-slate-500 font-medium">No reports found for the selected date range.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Original Modal remains for deeper analytics --}}
<div id="toolsModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeTools()"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xl font-bold text-slate-900">Advanced Analytics</h3>
            <button onclick="closeTools()" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-slate-900">
                <i class="hgi hgi-stroke hgi-multiply text-xl"></i>
            </button>
        </div>
        <div class="p-8 overflow-y-auto">
            <div id="modalChartContainer" style="height: 350px;"></div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

<script>
    // Load chart on page load to match sketch
    window.onload = function() {
        renderCharts();
    };

    function renderCharts() {
        const openedData = [@foreach ($stats as $stat) { label: "{{$stat->id}}", y: {{$stat->opened != NULL ? count(explode(',', $stat->opened)) : 0}} }, @endforeach];
        const clickedData = [@foreach ($stats as $stat) { label: "{{$stat->id}}", y: {{$stat->clicked != NULL ? count(explode(',', $stat->clicked)) : 0}} }, @endforeach];

        const chartConfig = {
            theme: "light2",
            animationEnabled: true,
            axisY: { gridThickness: 1, gridColor: "#f1f5f9", tickColor: "transparent", borderThickness: 0 },
            axisX: { tickColor: "transparent", borderThickness: 0 },
            data: [
                { type: "spline", color: "#10b981", name: "Opened", showInLegend: true, dataPoints: openedData },
                { type: "spline", color: "#3b82f6", name: "Clicked", showInLegend: true, dataPoints: clickedData }
            ]
        };

        new CanvasJS.Chart("mainChartContainer", chartConfig).render();
        new CanvasJS.Chart("modalChartContainer", chartConfig).render();
    }

    function openTools() {
        document.getElementById('toolsModal').classList.remove('hidden');
    }

    function closeTools() {
        document.getElementById('toolsModal').classList.add('hidden');
    }

    function printContent() { window.print(); }

    function downloadContent() {
        const target = document.getElementById('contentToDownload');
        html2canvas(target, { backgroundColor: '#f8fafc', scale: 2 }).then(canvas => {
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = `Campaign-Report-${new Date().toISOString().split('T')[0]}.png`;
            link.click();
        });
    }

    $(document).ready(function(){
        $('#weeklyReportCheckbox').change(function(){            
            $.ajax({
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                url: "{{ route('app.report.weekly.active') }}",
                method: "POST",
                data: { report_enable: $(this).prop('checked') ? 1 : 0 }
            }).then(() => { window.location.reload(); });            
        });
    });
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    body { font-family: 'Inter', sans-serif; }
    @media print {
        body * { visibility: hidden; }
        #contentToDownload, #contentToDownload * { visibility: visible; }
        #contentToDownload { position: absolute; left: 0; top: 0; width: 100%; padding: 20px; }
    }
</style>
@endsection