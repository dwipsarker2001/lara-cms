<div>
    <div class="text-[26px] font-semibold leading-none text-text-heading">{{ $widget->value }}</div>
    <div class="mt-2 flex items-center gap-1 text-[12px]">
        <span class="{{ $widget->up ? 'font-medium text-emerald-600' : 'font-medium text-red-500' }}">{{ $widget->delta }}</span>
        <span class="text-text-muted">vs last period</span>
    </div>
</div>
