<div x-data='{
    selected: "7 Days",
    periods: @json($widget->periodsData),
    get current() {
        return this.periods[this.selected] || this.periods["7 Days"];
    },
    getPath() {
        const pts = this.current.series;
        if (pts.length < 2) return "";
        const min = 0;
        const max = this.current.max;
        const range = max - min;
        const w = 700;
        const h = 200;
        const step = w / (pts.length - 1);
        
        let path = "";
        for (let i = 0; i < pts.length; i++) {
            const v = pts[i];
            const x = Math.round(step * i * 10) / 10;
            const y = Math.round((h - ((v - min) / range) * h) * 10) / 10;
            if (i === 0) {
                path += "M" + x + "," + y;
            } else {
                path += " L" + x + "," + y;
            }
        }
        return path;
    },
    getFillPath() {
        const linePath = this.getPath();
        if (!linePath) return "";
        return linePath + " L700,200 L0,200 Z";
    },
    getCirclesHtml() {
        const pts = this.current.series;
        const max = this.current.max;
        const length = pts.length;
        if (length === 0) return "";
        const w = 700;
        const h = 200;
        const step = length > 1 ? w / (length - 1) : w;
        
        let html = "";
        for (let i = 0; i < length; i++) {
            const v = pts[i];
            const cx = Math.round(step * i * 10) / 10;
            const cy = Math.round((h - (v / max) * h) * 10) / 10;
            html += "<circle cx=\"" + cx + "\" cy=\"" + cy + "\" r=\"4\" fill=\"#6366f1\" stroke=\"white\" stroke-width=\"2\" />";
        }
        return html;
    }
}' class="w-full">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2 text-[14px] font-medium text-text-heading">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4 text-text-muted"><path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 5-5"/></svg>
            Website Analytics
        </div>
        <div class="flex items-center gap-1 rounded-lg bg-gray-100 p-1">
            <template x-for="opt in ['Today', '7 Days', '30 Days', 'This Year']">
                <button
                    @click="selected = opt"
                    class="rounded-md px-2.5 py-1 text-[11px] font-medium transition-colors"
                    :class="selected === opt ? 'bg-white text-text-heading shadow-sm' : 'text-text-muted hover:text-text-heading'"
                    x-text="opt"
                ></button>
            </template>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-3 mb-5">
        <template x-for="metric in current.metrics">
            <div class="rounded-xl bg-gray-50 border border-gray-100 p-3">
                <div class="text-[11px] font-medium text-text-muted" x-text="metric.label"></div>
                <div class="mt-1 text-xl font-semibold text-text-heading" x-text="metric.value"></div>
                <div class="mt-0.5 text-[11px]" :class="metric.up ? 'font-medium text-emerald-600' : 'font-medium text-red-500'" x-text="metric.delta"></div>
            </div>
        </template>
    </div>

    <div class="relative h-52">
        <svg viewBox="0 0 700 200" class="w-full h-full" preserveAspectRatio="none">
            <defs>
                <linearGradient id="wa-gradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#6366f1" stop-opacity="0.2" />
                    <stop offset="100%" stop-color="#6366f1" stop-opacity="0" />
                </linearGradient>
            </defs>
            <path :d="getFillPath()" fill="url(#wa-gradient)" />
            <path :d="getPath()" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            <g x-html="getCirclesHtml()"></g>
        </svg>
        <div class="absolute inset-x-0 bottom-0 flex justify-between px-1">
            <template x-for="day in current.days">
                <span class="text-[11px] text-text-muted" x-text="day"></span>
            </template>
        </div>
    </div>
</div>
