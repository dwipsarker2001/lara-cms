<div x-data="websiteAnalytics()" class="w-full">
    <!-- Metrics Grid (3 columns: Visitors, Page Views, Avg. Duration) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
        <template x-for="metric in current.metrics">
            <div class="rounded-xl bg-gray-50 border border-gray-100 p-3.5">
                <div class="text-[11px] font-medium text-text-muted" x-text="metric.label"></div>
                <div class="mt-1.5 text-2xl font-bold text-text-heading" x-text="metric.value"></div>
                <div class="mt-1 text-[11px]" :class="metric.up ? 'font-medium text-emerald-600' : 'font-medium text-red-500'" x-text="metric.delta"></div>
            </div>
        </template>
    </div>

    <!-- Chart Container -->
    <div class="w-full">
        <div x-ref="chartContainer" class="w-full h-full min-h-[220px]"></div>
    </div>
</div>

@once
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endonce

<script>
    (function () {
        function registerWebsiteAnalytics() {
            if (window.Alpine.components && window.Alpine.components.websiteAnalytics) {
                return;
            }
            window.Alpine.data('websiteAnalytics', () => ({
                selected: '7 Days',
                periods: @json($widget->periodsData),
                chart: null,

                get current() {
                    return this.periods[this.selected] || this.periods['7 Days'];
                },

                init() {
                    window.addEventListener('analytics-period-change', (e) => {
                        if (e.detail) {
                            this.updatePeriod(e.detail);
                        }
                    });
                    const options = {
                        series: [{
                            name: 'Page Views',
                            data: this.periods[this.selected].series
                        }],
                        chart: {
                            type: 'area',
                            height: 220,
                            sparkline: {
                                enabled: false
                            },
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 350
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2.5,
                            colors: ['#6366f1']
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.35,
                                opacityTo: 0.05,
                                stops: [0, 95, 100],
                                colorStops: [
                                    {
                                        offset: 0,
                                        color: '#6366f1',
                                        opacity: 0.35
                                    },
                                    {
                                        offset: 100,
                                        color: '#6366f1',
                                        opacity: 0.05
                                    }
                                ]
                            }
                        },
                        xaxis: {
                            categories: this.periods[this.selected].days,
                            labels: {
                                show: true,
                                style: {
                                    colors: '#64748b',
                                    fontSize: '10px',
                                    fontFamily: 'inherit'
                                }
                            },
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            }
                        },
                        yaxis: {
                            labels: {
                                show: true,
                                style: {
                                    colors: '#94a3b8',
                                    fontSize: '10px'
                                }
                            }
                        },
                        grid: {
                            show: true,
                            borderColor: '#f1f5f9',
                            xaxis: {
                                lines: {
                                    show: false
                                }
                            },
                            yaxis: {
                                lines: {
                                    show: true
                                }
                            },
                            padding: {
                                top: 0,
                                right: 0,
                                bottom: 0,
                                left: 10
                            }
                        },
                        markers: {
                            size: 4,
                            colors: ['#6366f1'],
                            strokeColors: '#fff',
                            strokeWidth: 2,
                            hover: {
                                size: 6
                            }
                        },
                        tooltip: {
                            theme: 'dark',
                            x: {
                                show: true
                            },
                            y: {
                                formatter: function(val) {
                                    return val + ' Views';
                                }
                            }
                        }
                    };

                    this.chart = new ApexCharts(this.$refs.chartContainer, options);
                    this.chart.render();
                },

                updatePeriod(period) {
                    this.selected = period;
                    const data = this.periods[period];
                    this.chart.updateOptions({
                        xaxis: {
                            categories: data.days
                        }
                    });
                    this.chart.updateSeries([{
                        data: data.series
                    }]);
                }
            }));
        }

        if (window.Alpine) {
            registerWebsiteAnalytics();
        } else {
            document.addEventListener('alpine:init', registerWebsiteAnalytics);
        }
    })();
</script>
