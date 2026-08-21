<div x-data="{
    counts: {{ json_encode($counts) }},
    months: {{ json_encode($months) }},
    chart: null,
    init() {
        this.$nextTick(() => this.renderChart());
    },
    renderChart() {
        const options = {
            series: [{ name: 'Pages Published', data: this.counts }],
            chart: {
                type: 'bar',
                height: 200,
                toolbar: { show: false },
                animations: { enabled: true, speed: 350 }
            },
            plotOptions: {
                bar: { borderRadius: 5, columnWidth: '55%' }
            },
            dataLabels: { enabled: false },
            colors: ['#6366f1'],
            xaxis: {
                categories: this.months,
                labels: {
                    style: { colors: '#94a3b8', fontSize: '10px', fontFamily: 'inherit' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8', fontSize: '10px' }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } },
                padding: { top: 0, right: 0, bottom: 0, left: 5 }
            },
            tooltip: {
                theme: 'dark',
                y: { formatter: val => val + ' pages' }
            }
        };
        if (window.ApexCharts) {
            this.chart = new ApexCharts(this.$refs.chartEl, options);
            this.chart.render();
        }
    }
}">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <div class="text-[24px] font-bold leading-none text-text-heading">{{ $total }}</div>
            <div class="mt-1 flex items-center gap-1 text-[12px]">
                <span class="{{ $up ? 'font-medium text-emerald-600' : 'font-medium text-red-500' }}">{{ $delta }}</span>
                <span class="text-text-muted">this month</span>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-2.5 py-1 text-[11px] font-medium text-indigo-700">
            <span class="size-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
            Live
        </span>
    </div>
    @once
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endonce
    <div x-ref="chartEl" class="w-full min-h-[200px]"></div>
</div>
