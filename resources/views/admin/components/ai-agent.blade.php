{{-- =========================================================================
     Lara-CMS Autonomous Interactive AI Agent (Super AI Style)
     Clean Minimalist UI: Seamless Borderless Input, Theme Send Button, Lucide Icons
     ========================================================================= --}}
<div
    x-data="aiAgent()"
    x-init="init()"
    class="ai-agent-system font-sans select-none"
>
    <style>
        .ai-chat-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(209, 213, 219, 0.7) transparent;
        }
        .ai-chat-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .ai-chat-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .ai-chat-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(209, 213, 219, 0.7);
            border-radius: 9999px;
        }
        .ai-chat-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.9);
        }

        .ai-input-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(209, 213, 219, 0.6) transparent;
        }
        .ai-input-scroll::-webkit-scrollbar {
            width: 3px;
        }
        .ai-input-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .ai-input-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(209, 213, 219, 0.6);
            border-radius: 9999px;
        }
        .ai-input-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.9);
        }

        @keyframes ai-shimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
        }
        .ai-shimmer-text {
            background: linear-gradient(90deg, #94a3b8 0%, #ffffff 50%, #94a3b8 100%);
            background-size: 200% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: ai-shimmer 2s infinite linear;
        }
    </style>
    {{-- ===================================================
         1. Frosted Backdrop Overlay (Smooth Blur)
    =================================================== --}}
    <div
        x-show="isOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 backdrop-blur-none"
        x-transition:enter-end="opacity-100 backdrop-blur-sm"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 backdrop-blur-sm"
        x-transition:leave-end="opacity-0 backdrop-blur-none"
        class="fixed inset-0 z-[110] bg-black/20 backdrop-blur-sm"
        @click="closeChat()"
    ></div>

    {{-- ===================================================
         2. Floating Draggable Creature Avatar (Always Visible)
    =================================================== --}}
    <div
        class="fixed z-[125] group"
        :style="`left: ${posX}px; top: ${posY}px; touch-action: none;`"
        x-show="!isOpen || isDragging"
        x-cloak
    >
        {{-- Library-Style Animated Tooltip with Caret Arrow & Shimmer Text (Positioned clearly above avatar) --}}
        <div
            x-show="floatingStatusText"
            x-cloak
            x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-250"
            x-transition:enter-start="opacity-0 translate-y-1.5 scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
            class="absolute bottom-full mb-2.5 left-1/2 -translate-x-1/2 origin-bottom whitespace-nowrap pointer-events-none z-20 select-none drop-shadow-[0_4px_12px_rgba(0,0,0,0.3)]"
        >
            <div class="relative px-2.5 py-0.5 rounded-md bg-slate-950/95 backdrop-blur-md border border-white/15 flex items-center justify-center">
                <span class="ai-shimmer-text text-[11px] font-medium tracking-wide" x-text="floatingStatusText"></span>

                {{-- Tooltip Caret / Arrow Pointing Down at Avatar --}}
                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-slate-950 rotate-45 border-r border-b border-white/15"></div>
            </div>
        </div>

        {{-- Floating circular avatar button (Vibrant Gradient Ambient Base - Tight snug fit) --}}
        <div
            @pointerdown="startDrag($event)"
            @pointermove="onDragMove($event)"
            @pointerup="endDrag($event)"
            @pointercancel="endDrag($event)"
            class="relative size-13 sm:size-14 rounded-full bg-gradient-to-tr from-indigo-500 via-purple-500 to-cyan-400 p-0 shadow-[0_6px_22px_rgba(99,102,241,0.32),0_1px_3px_rgba(0,0,0,0.1)] hover:shadow-[0_10px_30px_rgba(99,102,241,0.48)] hover:scale-105 active:scale-95 transition-all duration-200 cursor-grab active:cursor-grabbing flex items-center justify-center overflow-visible select-none"
            style="touch-action: none; -webkit-user-drag: none; user-select: none;"
            :class="(isLoading || isProcessingActions) ? 'ring-2 ring-cyan-400/40 animate-pulse' : ''"
        >
            <div class="size-full rounded-full bg-slate-950/10 flex items-center justify-center overflow-visible p-0">
                {{-- SVG Animated Creature with Mouse-Tracking Interactive Eyes & Rich Colors --}}
                <svg
                    viewBox="-125 -125 250 250"
                    class="size-full object-contain pointer-events-none select-none transition-transform duration-300 overflow-visible"
                    :class="(isLoading || isProcessingActions) ? 'scale-105' : ''"
                    role="img"
                    aria-label="AI Agent Avatar"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <defs>
                        {{-- Vibrant 3D Body Gradient --}}
                        <linearGradient id="ai-body-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#6366f1"/>
                            <stop offset="48%" stop-color="#8b5cf6"/>
                            <stop offset="100%" stop-color="#06b6d4"/>
                        </linearGradient>

                        {{-- Specular Highlight Sheen --}}
                        <linearGradient id="ai-sheen" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff" stop-opacity="0.38"/>
                            <stop offset="55%" stop-color="#ffffff" stop-opacity="0"/>
                        </linearGradient>

                        {{-- Body shape mask for interior plates --}}
                        <mask id="bot-body-mask" maskUnits="userSpaceOnUse" x="-158" y="-158" width="316" height="316">
                            <path d="M100.19 0.33C100.19 3.6 100.03 6.88 99.71 10.13C99.39 13.38 98.9 16.63 98.27 19.84C97.63 23.04 96.83 26.23 95.88 29.36C94.93 32.48 93.83 35.58 92.58 38.6C91.33 41.62 89.92 44.59 88.38 47.47C86.84 50.35 85.15 53.17 83.33 55.89C81.52 58.6 79.56 61.24 77.49 63.77C75.42 66.29 73.21 68.73 70.9 71.04C68.59 73.35 66.15 75.56 63.63 77.63C61.1 79.7 58.46 81.66 55.74 83.48C53.03 85.29 50.21 86.98 47.33 88.52C44.45 90.06 41.47 91.47 38.46 92.72C35.44 93.97 32.34 95.07 29.22 96.02C26.09 96.97 22.9 97.77 19.7 98.41C16.49 99.04 13.24 99.53 9.99 99.85C6.74 100.17 3.45 100.33 0.19 100.33C-3.08 100.33 -6.36 100.17 -9.61 99.85C-12.87 99.53 -16.12 99.04 -19.32 98.41C-22.53 97.77 -25.71 96.97 -28.84 96.02C-31.97 95.07 -35.06 93.97 -38.08 92.72C-41.1 91.47 -44.07 90.06 -46.95 88.52C-49.83 86.98 -52.65 85.29 -55.37 83.48C-58.09 81.66 -60.73 79.7 -63.25 77.63C-65.78 75.56 -68.21 73.35 -70.52 71.04C-72.83 68.73 -75.04 66.29 -77.11 63.77C-79.19 61.24 -81.14 58.6 -82.96 55.89C-84.77 53.17 -86.46 50.35 -88 47.47C-89.54 44.59 -90.95 41.62 -92.2 38.6C-93.45 35.58 -94.56 32.48 -95.51 29.36C-96.46 26.23 -97.25 23.04 -97.89 19.84C-98.53 16.63 -99.01 13.38 -99.33 10.13C-99.65 6.88 -99.81 3.6 -99.81 0.33C-99.81 -2.94 -99.65 -6.22 -99.33 -9.47C-99.01 -12.72 -98.53 -15.98 -97.89 -19.18C-97.25 -22.38 -96.46 -25.57 -95.51 -28.7C-94.56 -31.83 -93.45 -34.92 -92.2 -37.94C-90.95 -40.96 -89.54 -43.93 -88 -46.81C-86.46 -49.69 -84.77 -52.51 -82.96 -55.23C-81.14 -57.94 -79.19 -60.58 -77.11 -63.11C-75.04 -65.64 -72.83 -68.07 -70.52 -70.38C-68.21 -72.69 -65.78 -74.9 -63.25 -76.97C-60.73 -79.04 -58.09 -81 -55.37 -82.82C-52.65 -84.63 -49.83 -86.32 -46.95 -87.86C-44.07 -89.4 -41.1 -90.81 -38.08 -92.06C-35.06 -93.31 -31.97 -94.42 -28.84 -95.37C-25.71 -96.31 -22.53 -97.11 -19.32 -97.75C-16.12 -98.39 -12.87 -98.87 -9.61 -99.19C-6.36 -99.51 -3.08 -99.67 0.19 -99.67C3.45 -99.67 6.74 -99.51 9.99 -99.19C13.24 -98.87 16.49 -98.39 19.7 -97.75C22.9 -97.11 26.09 -96.31 29.22 -95.37C32.34 -94.42 35.44 -93.31 38.46 -92.06C41.47 -90.81 44.45 -89.4 47.33 -87.86C50.21 -86.32 53.03 -84.63 55.74 -82.82C58.46 -81 61.1 -79.04 63.63 -76.97C66.15 -74.9 68.59 -72.69 70.9 -70.38C73.21 -68.07 75.42 -65.64 77.49 -63.11C79.56 -60.58 81.52 -57.94 83.33 -55.23C85.15 -52.51 86.84 -49.69 88.38 -46.81C89.92 -43.93 91.33 -40.96 92.58 -37.94C93.83 -34.92 94.93 -31.83 95.88 -28.7C96.83 -25.57 97.63 -22.38 98.27 -19.18C98.9 -15.98 99.39 -12.72 99.71 -9.47C100.03 -6.22 100.19 -2.94 100.19 0.33Z" fill="#fff"/>
                        </mask>

                        {{-- Visor mask with dynamic eye cutouts --}}
                        <mask id="bot-mask-interactive" maskUnits="userSpaceOnUse" x="-158" y="-158" width="316" height="316">
                            {{-- Body shape in mask (white reveals the dark face) --}}
                            <path d="M100.19 0.33C100.19 3.6 100.03 6.88 99.71 10.13C99.39 13.38 98.9 16.63 98.27 19.84C97.63 23.04 96.83 26.23 95.88 29.36C94.93 32.48 93.83 35.58 92.58 38.6C91.33 41.62 89.92 44.59 88.38 47.47C86.84 50.35 85.15 53.17 83.33 55.89C81.52 58.6 79.56 61.24 77.49 63.77C75.42 66.29 73.21 68.73 70.9 71.04C68.59 73.35 66.15 75.56 63.63 77.63C61.1 79.7 58.46 81.66 55.74 83.48C53.03 85.29 50.21 86.98 47.33 88.52C44.45 90.06 41.47 91.47 38.46 92.72C35.44 93.97 32.34 95.07 29.22 96.02C26.09 96.97 22.9 97.77 19.7 98.41C16.49 99.04 13.24 99.53 9.99 99.85C6.74 100.17 3.45 100.33 0.19 100.33C-3.08 100.33 -6.36 100.17 -9.61 99.85C-12.87 99.53 -16.12 99.04 -19.32 98.41C-22.53 97.77 -25.71 96.97 -28.84 96.02C-31.97 95.07 -35.06 93.97 -38.08 92.72C-41.1 91.47 -44.07 90.06 -46.95 88.52C-49.83 86.98 -52.65 85.29 -55.37 83.48C-58.09 81.66 -60.73 79.7 -63.25 77.63C-65.78 75.56 -68.21 73.35 -70.52 71.04C-72.83 68.73 -75.04 66.29 -77.11 63.77C-79.19 61.24 -81.14 58.6 -82.96 55.89C-84.77 53.17 -86.46 50.35 -88 47.47C-89.54 44.59 -90.95 41.62 -92.2 38.6C-93.45 35.58 -94.56 32.48 -95.51 29.36C-96.46 26.23 -97.25 23.04 -97.89 19.84C-98.53 16.63 -99.01 13.38 -99.33 10.13C-99.65 6.88 -99.81 3.6 -99.81 0.33C-99.81 -2.94 -99.65 -6.22 -99.33 -9.47C-99.01 -12.72 -98.53 -15.98 -97.89 -19.18C-97.25 -22.38 -96.46 -25.57 -95.51 -28.7C-94.56 -31.83 -93.45 -34.92 -92.2 -37.94C-90.95 -40.96 -89.54 -43.93 -88 -46.81C-86.46 -49.69 -84.77 -52.51 -82.96 -55.23C-81.14 -57.94 -79.19 -60.58 -77.11 -63.11C-75.04 -65.64 -72.83 -68.07 -70.52 -70.38C-68.21 -72.69 -65.78 -74.9 -63.25 -76.97C-60.73 -79.04 -58.09 -81 -55.37 -82.82C-52.65 -84.63 -49.83 -86.32 -46.95 -87.86C-44.07 -89.4 -41.1 -90.81 -38.08 -92.06C-35.06 -93.31 -31.97 -94.42 -28.84 -95.37C-25.71 -96.31 -22.53 -97.11 -19.32 -97.75C-16.12 -98.39 -12.87 -98.87 -9.61 -99.19C-6.36 -99.51 -3.08 -99.67 0.19 -99.67C3.45 -99.67 6.74 -99.51 9.99 -99.19C13.24 -98.87 16.49 -98.39 19.7 -97.75C22.9 -97.11 26.09 -96.31 29.22 -95.37C32.34 -94.42 35.44 -93.31 38.46 -92.06C41.47 -90.81 44.45 -89.4 47.33 -87.86C50.21 -86.32 53.03 -84.63 55.74 -82.82C58.46 -81 61.1 -79.04 63.63 -76.97C66.15 -74.9 68.59 -72.69 70.9 -70.38C73.21 -68.07 75.42 -65.64 77.49 -63.11C79.56 -60.58 81.52 -57.94 83.33 -55.23C85.15 -52.51 86.84 -49.69 88.38 -46.81C89.92 -43.93 91.33 -40.96 92.58 -37.94C93.83 -34.92 94.93 -31.83 95.88 -28.7C96.83 -25.57 97.63 -22.38 98.27 -19.18C98.9 -15.98 99.39 -12.72 99.71 -9.47C100.03 -6.22 100.19 -2.94 100.19 0.33Z" fill="#fff"/>
                            
                            {{-- Left Eye cutout --}}
                            <g :style="`transform: translate(${eyeBasePos.leftX + eyeX}px, ${eyeBasePos.leftY + eyeY}px) scale(1, ${eyeScaleY}); transform-origin: 0px 0px;`">
                                <path :d="leftEyePath" opacity="1" fill="#000"/>
                            </g>

                            {{-- Right Eye cutout --}}
                            <g :style="`transform: translate(${eyeBasePos.rightX + eyeX}px, ${eyeBasePos.rightY + eyeY}px) scale(1, ${eyeScaleY}); transform-origin: 0px 0px;`">
                                <path :d="rightEyePath" opacity="1" fill="#000"/>
                            </g>
                        </mask>
                    </defs>

                    {{-- 1. Outer Body: Gradient --}}
                    <path d="M100.19 0.33C100.19 3.6 100.03 6.88 99.71 10.13C99.39 13.38 98.9 16.63 98.27 19.84C97.63 23.04 96.83 26.23 95.88 29.36C94.93 32.48 93.83 35.58 92.58 38.6C91.33 41.62 89.92 44.59 88.38 47.47C86.84 50.35 85.15 53.17 83.33 55.89C81.52 58.6 79.56 61.24 77.49 63.77C75.42 66.29 73.21 68.73 70.9 71.04C68.59 73.35 66.15 75.56 63.63 77.63C61.1 79.7 58.46 81.66 55.74 83.48C53.03 85.29 50.21 86.98 47.33 88.52C44.45 90.06 41.47 91.47 38.46 92.72C35.44 93.97 32.34 95.07 29.22 96.02C26.09 96.97 22.9 97.77 19.7 98.41C16.49 99.04 13.24 99.53 9.99 99.85C6.74 100.17 3.45 100.33 0.19 100.33C-3.08 100.33 -6.36 100.17 -9.61 99.85C-12.87 99.53 -16.12 99.04 -19.32 98.41C-22.53 97.77 -25.71 96.97 -28.84 96.02C-31.97 95.07 -35.06 93.97 -38.08 92.72C-41.1 91.47 -44.07 90.06 -46.95 88.52C-49.83 86.98 -52.65 85.29 -55.37 83.48C-58.09 81.66 -60.73 79.7 -63.25 77.63C-65.78 75.56 -68.21 73.35 -70.52 71.04C-72.83 68.73 -75.04 66.29 -77.11 63.77C-79.19 61.24 -81.14 58.6 -82.96 55.89C-84.77 53.17 -86.46 50.35 -88 47.47C-89.54 44.59 -90.95 41.62 -92.2 38.6C-93.45 35.58 -94.56 32.48 -95.51 29.36C-96.46 26.23 -97.25 23.04 -97.89 19.84C-98.53 16.63 -99.01 13.38 -99.33 10.13C-99.65 6.88 -99.81 3.6 -99.81 0.33C-99.81 -2.94 -99.65 -6.22 -99.33 -9.47C-99.01 -12.72 -98.53 -15.98 -97.89 -19.18C-97.25 -22.38 -96.46 -25.57 -95.51 -28.7C-94.56 -31.83 -93.45 -34.92 -92.2 -37.94C-90.95 -40.96 -89.54 -43.93 -88 -46.81C-86.46 -49.69 -84.77 -52.51 -82.96 -55.23C-81.14 -57.94 -79.19 -60.58 -77.11 -63.11C-75.04 -65.64 -72.83 -68.07 -70.52 -70.38C-68.21 -72.69 -65.78 -74.9 -63.25 -76.97C-60.73 -79.04 -58.09 -81 -55.37 -82.82C-52.65 -84.63 -49.83 -86.32 -46.95 -87.86C-44.07 -89.4 -41.1 -90.81 -38.08 -92.06C-35.06 -93.31 -31.97 -94.42 -28.84 -95.37C-25.71 -96.31 -22.53 -97.11 -19.32 -97.75C-16.12 -98.39 -12.87 -98.87 -9.61 -99.19C-6.36 -99.51 -3.08 -99.67 0.19 -99.67C3.45 -99.67 6.74 -99.51 9.99 -99.19C13.24 -98.87 16.49 -98.39 19.7 -97.75C22.9 -97.11 26.09 -96.31 29.22 -95.37C32.34 -94.42 35.44 -93.31 38.46 -92.06C41.47 -90.81 44.45 -89.4 47.33 -87.86C50.21 -86.32 53.03 -84.63 55.74 -82.82C58.46 -81 61.1 -79.04 63.63 -76.97C66.15 -74.9 68.59 -72.69 70.9 -70.38C73.21 -68.07 75.42 -65.64 77.49 -63.11C79.56 -60.58 81.52 -57.94 83.33 -55.23C85.15 -52.51 86.84 -49.69 88.38 -46.81C89.92 -43.93 91.33 -40.96 92.58 -37.94C93.83 -34.92 94.93 -31.83 95.88 -28.7C96.83 -25.57 97.63 -22.38 98.27 -19.18C98.9 -15.98 99.39 -12.72 99.71 -9.47C100.03 -6.22 100.19 -2.94 100.19 0.33Z" fill="url(#ai-body-grad)"/>

                    {{-- 2. Specular Highlight --}}
                    <path d="M100.19 0.33C100.19 3.6 100.03 6.88 99.71 10.13C99.39 13.38 98.9 16.63 98.27 19.84C97.63 23.04 96.83 26.23 95.88 29.36C94.93 32.48 93.83 35.58 92.58 38.6C91.33 41.62 89.92 44.59 88.38 47.47C86.84 50.35 85.15 53.17 83.33 55.89C81.52 58.6 79.56 61.24 77.49 63.77C75.42 66.29 73.21 68.73 70.9 71.04C68.59 73.35 66.15 75.56 63.63 77.63C61.1 79.7 58.46 81.66 55.74 83.48C53.03 85.29 50.21 86.98 47.33 88.52C44.45 90.06 41.47 91.47 38.46 92.72C35.44 93.97 32.34 95.07 29.22 96.02C26.09 96.97 22.9 97.77 19.7 98.41C16.49 99.04 13.24 99.53 9.99 99.85C6.74 100.17 3.45 100.33 0.19 100.33C-3.08 100.33 -6.36 100.17 -9.61 99.85C-12.87 99.53 -16.12 99.04 -19.32 98.41C-22.53 97.77 -25.71 96.97 -28.84 96.02C-31.97 95.07 -35.06 93.97 -38.08 92.72C-41.1 91.47 -44.07 90.06 -46.95 88.52C-49.83 86.98 -52.65 85.29 -55.37 83.48C-58.09 81.66 -60.73 79.7 -63.25 77.63C-65.78 75.56 -68.21 73.35 -70.52 71.04C-72.83 68.73 -75.04 66.29 -77.11 63.77C-79.19 61.24 -81.14 58.6 -82.96 55.89C-84.77 53.17 -86.46 50.35 -88 47.47C-89.54 44.59 -90.95 41.62 -92.2 38.6C-93.45 35.58 -94.56 32.48 -95.51 29.36C-96.46 26.23 -97.25 23.04 -97.89 19.84C-98.53 16.63 -99.01 13.38 -99.33 10.13C-99.65 6.88 -99.81 3.6 -99.81 0.33C-99.81 -2.94 -99.65 -6.22 -99.33 -9.47C-99.01 -12.72 -98.53 -15.98 -97.89 -19.18C-97.25 -22.38 -96.46 -25.57 -95.51 -28.7C-94.56 -31.83 -93.45 -34.92 -92.2 -37.94C-90.95 -40.96 -89.54 -43.93 -88 -46.81C-86.46 -49.69 -84.77 -52.51 -82.96 -55.23C-81.14 -57.94 -79.19 -60.58 -77.11 -63.11C-75.04 -65.64 -72.83 -68.07 -70.52 -70.38C-68.21 -72.69 -65.78 -74.9 -63.25 -76.97C-60.73 -79.04 -58.09 -81 -55.37 -82.82C-52.65 -84.63 -49.83 -86.32 -46.95 -87.86C-44.07 -89.4 -41.1 -90.81 -38.08 -92.06C-35.06 -93.31 -31.97 -94.42 -28.84 -95.37C-25.71 -96.31 -22.53 -97.11 -19.32 -97.75C-16.12 -98.39 -12.87 -98.87 -9.61 -99.19C-6.36 -99.51 -3.08 -99.67 0.19 -99.67C3.45 -99.67 6.74 -99.51 9.99 -99.19C13.24 -98.87 16.49 -98.39 19.7 -97.75C22.9 -97.11 26.09 -96.31 29.22 -95.37C32.34 -94.42 35.44 -93.31 38.46 -92.06C41.47 -90.81 44.45 -89.4 47.33 -87.86C50.21 -86.32 53.03 -84.63 55.74 -82.82C58.46 -81 61.1 -79.04 63.63 -76.97C66.15 -74.9 68.59 -72.69 70.9 -70.38C73.21 -68.07 75.42 -65.64 77.49 -63.11C79.56 -60.58 81.52 -57.94 83.33 -55.23C85.15 -52.51 86.84 -49.69 88.38 -46.81C89.92 -43.93 91.33 -40.96 92.58 -37.94C93.83 -34.92 94.93 -31.83 95.88 -28.7C96.83 -25.57 97.63 -22.38 98.27 -19.18C98.9 -15.98 99.39 -12.72 99.71 -9.47C100.03 -6.22 100.19 -2.94 100.19 0.33Z" fill="url(#ai-sheen)"/>

                    {{-- 3. Crisp Eye Glow Under-Plate --}}
                    <g mask="url(#bot-body-mask)">
                        <rect x="-158" y="-158" width="316" height="316" fill="#ffffff"/>
                    </g>

                    {{-- 4. Deep Obsidian Visor with Eye Cutouts --}}
                    <g mask="url(#bot-mask-interactive)">
                        <rect x="-158" y="-158" width="316" height="316" fill="#0c101d"/>
                        {{-- Cute Rosy Cheeks --}}
                        <ellipse cx="-44" cy="24" rx="8" ry="4" fill="#f43f5e" opacity="0.32" x-show="isOpen || isProcessingActions"/>
                        <ellipse cx="64" cy="24" rx="8" ry="4" fill="#f43f5e" opacity="0.32" x-show="isOpen || isProcessingActions"/>
                    </g>
                </svg>
            </div>
        </div>
    </div>

    {{-- ===================================================
         3. Super AI Modal Window
    =================================================== --}}
    <div
        x-show="isOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="fixed z-[120] inset-4 sm:inset-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 flex flex-col bg-white rounded-2xl shadow-[0_16px_48px_rgba(0,0,0,0.12),0_0_0_1px_rgba(0,0,0,0.06)] border border-gray-100 overflow-hidden transition-all duration-200"
        :class="isExpanded ? 'sm:w-[90vw] sm:max-w-4xl sm:h-[85vh]' : 'sm:w-[460px] sm:h-[590px]'"
    >
        {{-- Clean Super AI Header (Balanced Tight Spacing) --}}
        <div class="px-4 py-2.5 bg-white flex items-center justify-between shrink-0 border-b border-gray-100/60">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-semibold text-gray-900 tracking-tight">Ask Super AI</h3>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-200/60 font-mono">
                    {{ \App\Services\AiAgentService::getActiveModelName() }}
                </span>
            </div>

            <div class="flex items-center gap-1">
                {{-- Erase / Clear Chat --}}
                <button
                    type="button"
                    @click="clearChat()"
                    class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
                    title="Erase Chat"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                        <path d="m7 21-4.3-4.3c-1-1-1-2.5 0-3.4l9.6-9.6c1-1 2.5-1 3.4 0l5.6 5.6c1 1 1 2.5 0 3.4L13 21"/>
                        <path d="M22 21H7"/>
                        <path d="m5 11 9 9"/>
                    </svg>
                </button>

                {{-- Close Button (Lucide X) --}}
                <button
                    type="button"
                    @click="closeChat()"
                    class="p-1 rounded-lg text-gray-400 hover:text-gray-800 hover:bg-gray-100 transition-colors"
                    title="Close"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ===================================================
             Chat Conversation Stream
        =================================================== --}}
        <div class="flex-1 flex flex-col min-h-0 bg-white">
            {{-- Message List Container (Custom Minimalist Scrollbar) --}}
            <div
                x-ref="chatContainer"
                class="ai-chat-scroll flex-1 overflow-y-auto px-4 py-2 space-y-2.5"
                style="scrollbar-width: thin; scrollbar-color: rgba(226, 228, 233, 0.9) transparent;"
            >
                <template x-for="msg in messages" :key="msg.id">
                    <div class="flex flex-col" :class="msg.role === 'user' ? 'items-end' : 'items-start'">
                        {{-- Message Bubble Wrapper --}}
                        <div class="flex items-start gap-2 max-w-[92%]" :class="msg.role === 'user' ? 'flex-row-reverse' : 'flex-row'">
                            {{-- Avatar for AI Assistant (Standard Avatar) --}}
                            <template x-if="msg.role === 'assistant'">
                                <div class="size-7 shrink-0 flex items-center justify-center overflow-visible bg-transparent mt-0.5">
                                    <img src="/images/ai-agent-avatar.svg" alt="AI" class="size-6.5 object-contain pointer-events-none overflow-visible" />
                                </div>
                            </template>

                            {{-- Message Content Box --}}
                            <div class="flex flex-col gap-1 min-w-0">
                                <div
                                    class="text-xs sm:text-sm leading-relaxed break-words"
                                    :class="msg.role === 'user'
                                        ? 'bg-primary text-white font-medium rounded-xl rounded-tr-xs px-3.5 py-2 shadow-xs'
                                        : (msg.error
                                            ? 'bg-red-50 text-red-800 border border-red-200/80 rounded-xl rounded-tl-xs px-3 py-2'
                                            : 'bg-[#f4f5f7] text-gray-800 rounded-xl rounded-tl-xs px-3 py-2')"
                                >
                                    {{-- Markdown rendered content --}}
                                    <div
                                        class="prose prose-xs sm:prose-sm max-w-none prose-p:my-0.5 prose-headings:my-1"
                                        :class="msg.role === 'user' ? 'text-white prose-p:text-white prose-strong:text-white' : 'prose-strong:text-gray-900'"
                                        x-html="formatMarkdown(msg.content)"
                                    ></div>
                                </div>

                                {{-- Action execution summary Accordion (Seamless Borderless Design) --}}
                                <template x-if="msg.actions && msg.actions.length > 0">
                                    <div
                                        x-data="{ isExpanded: false }"
                                        class="mt-1.5 bg-[#f4f5f7] rounded-xl overflow-hidden shadow-2xs transition-all duration-200"
                                    >
                                        {{-- Accordion Toggle Header --}}
                                        <div
                                            @click="isExpanded = !isExpanded"
                                            class="px-3 py-2 flex items-center justify-between cursor-pointer hover:bg-[#e9ecef] select-none transition-colors"
                                        >
                                            <div class="flex items-center gap-2">
                                                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                                <span class="text-xs font-semibold text-gray-800">Changes Applied</span>
                                                <span class="px-1.5 py-0.2 rounded-full text-[10px] font-medium bg-white text-gray-700 font-mono shadow-2xs" x-text="msg.actions.length"></span>
                                                <svg
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2.25"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="size-3 text-gray-400 transition-transform duration-200"
                                                    :class="isExpanded ? 'rotate-180 text-gray-700' : ''"
                                                >
                                                    <path d="m6 9 6 6 6-6"/>
                                                </svg>
                                            </div>

                                            <div class="flex items-center gap-1.5" @click.stop>
                                                <template x-if="msg.canUndo && !msg.undone">
                                                    <button
                                                        type="button"
                                                        @click="undoMessage(msg.id)"
                                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-medium text-gray-700 bg-white hover:text-gray-900 hover:bg-white/90 transition-all shadow-2xs"
                                                        title="Undo these changes"
                                                    >
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-gray-500">
                                                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                                            <path d="M3 3v5h5"/>
                                                        </svg>
                                                        <span>Undo</span>
                                                    </button>
                                                </template>
                                                <template x-if="msg.undone">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded shadow-2xs">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3">
                                                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                                            <path d="M3 3v5h5"/>
                                                        </svg>
                                                        Reverted
                                                    </span>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Collapsible List of Change Items (Borderless) --}}
                                        <div
                                            x-show="isExpanded"
                                            x-cloak
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 -translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 -translate-y-1"
                                            class="px-3 pb-2.5 pt-1.5 bg-[#e9ecef]/60 space-y-1.5"
                                        >
                                            <div class="flex flex-wrap gap-1.5 pt-0.5">
                                                <template x-for="(act, aIdx) in msg.actions" :key="aIdx">
                                                    <button
                                                        type="button"
                                                        @click="navigateTo(act.section_index, act.field_path)"
                                                        class="group inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[11px] font-medium transition-all text-left shadow-2xs"
                                                        :class="act.action === 'add_section'
                                                            ? 'bg-emerald-100/80 hover:bg-emerald-200/80 text-emerald-950'
                                                            : (act.action === 'set_image'
                                                                ? 'bg-indigo-100/80 hover:bg-indigo-200/80 text-indigo-950'
                                                                : (act.action === 'remove_section'
                                                                    ? 'bg-rose-100/80 hover:bg-rose-200/80 text-rose-950'
                                                                    : 'bg-white hover:bg-white/95 text-gray-800'))"
                                                        :title="'Click to focus section #' + ((act.section_index || 0) + 1)"
                                                    >
                                                        {{-- Add Section Icon --}}
                                                        <template x-if="act.action === 'add_section'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-emerald-700 shrink-0">
                                                                    <circle cx="12" cy="12" r="10"/>
                                                                    <path d="M12 8v8M8 12h8"/>
                                                                </svg>
                                                                <span>Added <strong class="font-semibold" x-text="act.name"></strong></span>
                                                            </span>
                                                        </template>

                                                        {{-- Set Image Icon --}}
                                                        <template x-if="act.action === 'set_image'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-indigo-700 shrink-0">
                                                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                                                                    <circle cx="9" cy="9" r="2"/>
                                                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                                                </svg>
                                                                <span>Updated Image</span>
                                                            </span>
                                                        </template>

                                                        {{-- Update Field Icon --}}
                                                        <template x-if="act.action === 'update_field'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-gray-600 shrink-0">
                                                                    <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                                                </svg>
                                                                <span class="capitalize" x-text="(act.field_path || 'field').replace(/_/g, ' ')"></span>
                                                            </span>
                                                        </template>

                                                        {{-- Update Section Icon --}}
                                                        <template x-if="act.action === 'update_section'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-gray-600 shrink-0">
                                                                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                                                    <path d="m3.3 7 8.7 5 8.7-5M12 22V12"/>
                                                                </svg>
                                                                <span>Section #<span x-text="(act.section_index || 0) + 1"></span></span>
                                                            </span>
                                                        </template>

                                                        {{-- Remove Section Icon --}}
                                                        <template x-if="act.action === 'remove_section'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-rose-700 shrink-0">
                                                                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                                </svg>
                                                                <span>Removed Section #<span x-text="(act.section_index || 0) + 1"></span></span>
                                                            </span>
                                                        </template>

                                                        {{-- Save Page Icon --}}
                                                        <template x-if="act.action === 'save_page'">
                                                            <span class="inline-flex items-center gap-1">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3 text-emerald-700 shrink-0">
                                                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                                                    <polyline points="17 21 17 13 7 13 7 21"/>
                                                                    <polyline points="7 3 7 8 15 8"/>
                                                                </svg>
                                                                <span>Saved &amp; Published</span>
                                                            </span>
                                                        </template>

                                                        {{-- Subtle locator arrow indicator --}}
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-2.5 opacity-40 group-hover:opacity-100 group-hover:translate-x-0.5 transition-all ml-0.5">
                                                            <path d="M5 12h14M12 5l7 7-7 7"/>
                                                        </svg>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Loading / Thinking indicator inside chat modal (Dynamic Bloub Animation) --}}
                <div x-show="isLoading || isProcessingActions" x-cloak class="flex items-start gap-2.5 max-w-[85%]">
                    <div class="size-8 shrink-0 flex items-center justify-center overflow-visible bg-transparent mt-0.5">
                        <img :src="avatarSrc" alt="AI" class="size-7.5 object-contain animate-bounce overflow-visible" />
                    </div>
                    <div class="bg-[#f4f5f7] rounded-xl rounded-tl-xs px-3.5 py-2 text-xs flex items-center border border-gray-200/50">
                        <span class="font-medium text-gray-700 animate-pulse" x-text="statusMessage || 'Thinking & Crafting...'"></span>
                    </div>
                </div>
            </div>

            {{-- ===================================================
                 PromptInput System (Auto-expanding Textarea & Actions)
            =================================================== --}}
            <div class="px-3.5 pt-1 pb-3 bg-white shrink-0">
                <div class="relative rounded-2xl border border-gray-200/90 bg-[#f8fafc] shadow-2xs">
                    {{-- Auto-resizing Textarea (Shift+Enter for newline, Enter to send) --}}
                    <textarea
                        x-ref="promptInput"
                        x-model="prompt"
                        @input="adjustTextareaHeight($event.target)"
                        @keydown.enter.exact.prevent="if (!isLoading && !isProcessingActions) sendMessage()"
                        placeholder="Ask AI to write copy, add sections, or find images..."
                        rows="1"
                        class="ai-input-scroll w-full bg-transparent resize-none border-0 focus:ring-0 focus:outline-none text-xs sm:text-sm text-gray-900 placeholder-gray-400 px-3.5 pt-2.5 pb-1 max-h-[200px] min-h-[38px] leading-relaxed block overflow-y-hidden"
                        :disabled="isProcessingActions"
                    ></textarea>

                    {{-- Actions Row (Right-aligned Send / Stop Button) --}}
                    <div class="flex items-center justify-end px-2.5 pb-2 pt-0.5">
                        {{-- Stop Generation Button (when active) --}}
                        <button
                            type="button"
                            x-show="isLoading || isProcessingActions"
                            x-cloak
                            @click="stopGeneration()"
                            class="w-8 h-8 rounded-full bg-primary hover:bg-primary/90 text-white flex items-center justify-center transition-all shadow-xs active:scale-95 cursor-pointer shrink-0"
                            title="Stop generation"
                        >
                            <svg viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5">
                                <rect x="6" y="6" width="12" height="12" rx="2" />
                            </svg>
                        </button>

                        {{-- Send Message Button (when idle) --}}
                        <button
                            type="button"
                            x-show="!isLoading && !isProcessingActions"
                            @click="sendMessage()"
                            :disabled="!prompt.trim()"
                            class="w-8 h-8 rounded-full bg-primary hover:bg-primary/90 text-white flex items-center justify-center transition-all shadow-xs active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed disabled:active:scale-100 cursor-pointer shrink-0"
                            title="Send message"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <path d="m5 12 7-7 7 7"/>
                                <path d="M12 19V5"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
