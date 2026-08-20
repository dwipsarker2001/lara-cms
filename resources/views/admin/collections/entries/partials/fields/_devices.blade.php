{{-- devices (responsive visibility) --}}
<template x-if="field.type === 'devices'">
    <div x-data="{
        getDevVal() {
            let val = getField(field.name);
            if (typeof val === 'string') {
                try { val = JSON.parse(val); } catch(e) { val = {}; }
            }
            return (val && typeof val === 'object' && !Array.isArray(val)) ? val : {};
        },
        isDeviceActive(dev) {
            const val = this.getDevVal();
            return val[dev] !== false && val[dev] !== 'false';
        },
        toggleDevice(dev) {
            const val = this.getDevVal();
            const current = {
                laptop: val.laptop !== false && val.laptop !== 'false',
                tablet: val.tablet !== false && val.tablet !== 'false',
                mobile: val.mobile !== false && val.mobile !== 'false'
            };
            current[dev] = !current[dev];
            setField(field.name, current);
        }
    }">
        <div class="mb-1.5">
            <label class="block text-sm font-semibold text-text-primary" x-text="field.label"></label>
        </div>
        <div class="grid grid-cols-3 gap-2">
            <template x-for="dev in [{key:'laptop',label:'Laptop',title:'Toggle visibility on Laptop (≥ 1024px)'},{key:'tablet',label:'Tablet',title:'Toggle visibility on Tablet (768px - 1023px)'},{key:'mobile',label:'Mobile',title:'Toggle visibility on Mobile (< 768px)'}]" :key="dev.key">
                <button type="button" @click="toggleDevice(dev.key)"
                    class="flex items-center gap-2 bg-white rounded-lg border border-gray-300 px-2.5 py-2 shadow-[0_2px_3px_-2px_rgba(0,0,0,0.15)] hover:border-gray-400 transition-all select-none cursor-pointer text-left focus:outline-none"
                    :title="dev.title"
                >
                    <div class="size-4 shrink-0 rounded border flex items-center justify-center transition-colors"
                        :class="isDeviceActive(dev.key) ? 'bg-primary border-primary text-white' : 'border-gray-300 bg-white'"
                    >
                        <svg x-show="isDeviceActive(dev.key)" class="size-3 text-white" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </div>
                    <span class="text-xs font-semibold truncate" :class="isDeviceActive(dev.key) ? 'text-text-primary' : 'text-text-muted/60'" x-text="dev.label"></span>
                </button>
            </template>
        </div>
    </div>
</template>
