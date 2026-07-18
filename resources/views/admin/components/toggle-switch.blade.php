@props(['model', 'name', 'value' => 'false'])

<div x-data="{ {{ $model }}: {{ $value }} }">
    <div class="flex items-center justify-end h-full">
        <button type="button" role="switch"
            :aria-checked="{{ $model }}"
            :data-state="{{ $model }} ? 'checked' : 'unchecked'"
            @click="{{ $model }} = !{{ $model }}"
            class="relative flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/30 data-[state=checked]:shadow-inner data-[state=checked]:border-emerald-500 data-[state=checked]:bg-emerald-500 data-[state=unchecked]:!border-gray-300 data-[state=unchecked]:bg-gray-200"
        >
            <span :data-state="{{ $model }} ? 'checked' : 'unchecked'"
                class="my-auto flex items-center justify-center size-5 rounded-full bg-white text-xs shadow-[0_10px_15px_-3px_rgba(0,0,0,0.1),0_4px_6px_-4px_rgba(0,0,0,0.1)] transition-transform will-change-transform data-[state=checked]:translate-x-full data-[state=unchecked]:translate-x-0"
            ></span>
        </button>
        <input type="hidden" name="{{ $name }}" :value="{{ $model }} ? '1' : '0'">
    </div>
</div>
