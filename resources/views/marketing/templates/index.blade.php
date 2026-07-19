    @extends('marketing.layouts.app')
    <title>ACCOUNT : Templates</title>

    @section('content')
    <div class="w-full min-h-screen px-8 py-10 bg-[#f8fafc]" x-data="{ activeTab: 'featured' }">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-none">Email Templates</h1>
                <p class="text-slate-500 font-medium mt-3 max-w-xl">Choose a high-converting layout or start from a wireframe.</p>
            </div>
            <a href="{{ route('app.template.create-page') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-white font-semibold text-sm bg-gradient-to-br from-[#007682] to-[#408b86] hover:brightness-110 transition-all duration-300 active:scale-95 shadow-lg no-underline">
                <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M8 3v10M3 8h10" /></svg>
                Create Template
            </a>

            <div class="flex items-center border gap-2 p-0.5 bg-slate-200/50 w-fit rounded-full backdrop-blur-sm">
                <button @click="activeTab = 'featured'"
                    :class="activeTab === 'featured' ? 'bg-white text-slate-900 border' : 'text-slate-500 hover:text-slate-700'"
                    class="px-8 py-2.5 text-xs font-semibold uppercase rounded-full transition-all duration-300">
                    Featured
                </button>
                <button @click="activeTab = 'basic'"
                    :class="activeTab === 'basic' ? 'bg-white text-slate-900 border' : 'text-slate-500 hover:text-slate-700'"
                    class="px-8 py-2.5 text-xs font-semibold uppercase rounded-full transition-all duration-300">
                    Basic Layouts
                </button>
                <button @click="activeTab = 'user'"
                    :class="activeTab === 'user' ? 'bg-white text-slate-900 border' : 'text-slate-500 hover:text-slate-700'"
                    class="px-8 py-2.5 text-xs font-semibold uppercase rounded-full transition-all duration-300">
                    My Saved
                </button>
            </div>
        </div>


        @if (session('success'))
            <div class="max-w-[1600px] mx-auto mb-8 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 px-6 py-4 rounded-r-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="hgi hgi-stroke hgi-tick-01 text-xl"></i>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
                <button class="opacity-50 hover:opacity-100" onclick="this.parentElement.remove()">✕</button>
            </div>
        @endif


        <div class="max-w-[1600px] mx-auto mt-10">
            <div x-show="activeTab === 'featured'" x-cloak x-transition.opacity.duration.400ms class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @php
                    $dir = public_path("templates/featured/");
                    $templateUrl = is_dir($dir) ? array_diff(scandir($dir), ['..', '.']) : [];
                @endphp
                @foreach ($templateUrl as $name)
                    @if ($name != '0_3_form_builder' && file_exists($dir . $name . "/index.html"))
                        @php
                            $content = file_get_contents($dir . $name . "/index.html");
                            preg_match('/<title>(.*?)<\/title>/i', $content, $m);
                            $title = $m[1] ?? $name;
                        @endphp
                        <div class="group relative flex flex-col bg-white rounded-lg border border-slate-200 shadow-sm hover:shadow-sm transition-all duration-500 overflow-hidden hover:-translate-y-3">
                            <div class="relative h-[350px] overflow-hidden bg-slate-100">
                                <div class="absolute inset-0 bg-cover bg-top transition-all duration-[4000ms] ease-in-out group-hover:bg-bottom" 
                                    style="background-image: url('{{ asset('public/templates/featured/'. $name . '/thumb.png') }}')">
                                </div>
                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                    <form method="post" action="{{ route('app.template.select') }}">
                                        @csrf
                                        <input name="id" value="{{ $name }}" hidden />
                                        <input name="type" value="featured" hidden />
                                        <button type="submit"
                                            class="flex items-center gap-3 px-6 py-2 rounded-lg text-white 
                                                bg-gradient-to-br from-[#007682] to-[#408b86]
                                                hover:brightness-110
                                                transition-all duration-300 active:scale-95
                                                shadow-lg">
                                            <span class="font-bold text-sm">Use this template</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="px-8 py-3 bg-white border-t border-slate-50">
                                <div class="flex justify-between items-start">
                                    <h5 class="text-lg font-semibold text-slate-900">{{ $title }}</h5>
                                </div>
                                <p class="text-slate-400 text-[13px]">Default Template</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div x-show="activeTab === 'basic'" x-cloak x-transition.opacity.duration.400ms class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
                @php
                    $dir_default = public_path("templates/default/");
                    $templateUrl_default = is_dir($dir_default) ? array_diff(scandir($dir_default), ['..', '.']) : [];
                @endphp
                @foreach ($templateUrl_default as $name)
                    @if ($name != '0_3_form_builder' && file_exists($dir_default . $name . "/index.html"))
                        @php
                            $content = file_get_contents($dir_default . $name . "/index.html");
                            preg_match('/<title>(.*?)<\/title>/i', $content, $m);
                            $title = $m[1] ?? $name;
                            $thumbType = file_exists($dir_default . $name . '/thumb.svg') ? '.svg' : '.png';
                        @endphp
                        <div class="group relative flex flex-col bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-500 overflow-hidden hover:-translate-y-3">
                            <!-- Image Area -->
                            <div class="relative h-[350px] overflow-hidden bg-slate-100">
                                <!-- Background Image -->
                                <div class="absolute inset-0 bg-cover bg-top transition-all duration-[4000ms] ease-in-out group-hover:bg-bottom"
                                    style="background-image: url('{{ asset('public/templates/default/'. $name. '/thumb'. $thumbType) }}')">
                                </div>
                                <!-- Hover Overlay -->
                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                    <form method="post" action="{{ route('app.template.select') }}">
                                        @csrf
                                        <input name="id" value="{{ $name }}" hidden />
                                        <input name="type" value="default" hidden />

                                        <button type="submit"
                                            class="flex items-center gap-3 px-6 py-2 rounded-lg text-white 
                                                bg-gradient-to-br from-[#007682] to-[#408b86]
                                                hover:brightness-110
                                                transition-all duration-300 active:scale-95
                                                shadow-lg">
                                            <span class="font-bold text-sm">Use this template</span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Content Area -->
                            <div class="px-8 py-3 bg-white border-t border-slate-50">
                                <div class="flex justify-between items-start">
                                    <h5 class="text-lg font-semibold text-slate-900">{{ $title }}</h5>
                                </div>
                                <p class="text-slate-400 text-[13px]">
                                    Structural Layout
                                </p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            @if(count($mylist) != 0)
            <div x-show="activeTab === 'user'" x-cloak x-transition.opacity.duration.400ms class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
                @foreach ($mylist as $template)
                    @php $template_id = $template->template_id; @endphp
                    @if(file_exists(public_path("templates/user/" . $template_id . "/index.html")))
                    <div id="template_card_{{ $template_id }}"
                        class="group relative flex flex-col bg-white border 
                        {{ session('badge') == $template_id ? 'border-teal-500 ring-4 ring-teal-50' : 'border-slate-200' }}
                        rounded-xl overflow-hidden shadow-sm hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)]
                        transition-all duration-500 hover:-translate-y-3">
                        <div class="relative h-[350px] overflow-hidden bg-slate-100">
                            <div class="absolute inset-0 bg-cover bg-top transition-all duration-[4000ms] ease-in-out group-hover:bg-bottom"
                                style="background-image: url('{{ asset('public/templates/user/'. $template_id. '/thumb.png') }}')">
                            </div>
                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 
                                        transition-all duration-300 flex items-center justify-center 
                                        backdrop-blur-[3px]">
                                <div class="flex gap-4">
                                    <a href="{{ route('app.template.design', ['id' => $template_id, 'type' => 'user']) }}"
                                        class="w-12 h-12 flex items-center justify-center rounded-full text-white 
                                        bg-gradient-to-br from-[#007682] to-[#408b86]
                                        hover:brightness-110 transition-all duration-300 active:scale-95 shadow-lg">
                                        <i class="hgi hgi-stroke hgi-edit-02 text-lg"></i>
                                    </a>
                                    <button onclick="removeTemplate('{{ $template_id }}')"
                                        class="w-12 h-12 flex items-center justify-center rounded-full text-white 
                                        bg-gradient-to-br from-red-500 to-red-600
                                        hover:brightness-110 transition-all duration-300 active:scale-95 shadow-lg">
                                        <i class="hgi hgi-stroke hgi-delete-02 text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="px-8 py-3 bg-white border-t border-slate-50">
                            <h5 class="text-lg font-semibold text-slate-900 truncate">
                                {{ $template->name }}
                            </h5>
                            <p class="text-slate-400 text-[13px]">
                                Updated {{ $template->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endsection

    @section('script')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function removeTemplate(id) {
            swal({
                title: "Delete Design?",
                text: "This template will be removed forever.",
                icon: "warning",
                buttons: ["Keep it", "Delete"],
                dangerMode: true,
                className: "rounded-3xl",
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('app.template.remove') }}",
                        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                        method: "post",
                        data: { template_id: id },
                        success: function() {
                            const el = document.getElementById("template_card_" + id);
                            el.style.opacity = '0';
                            el.style.transform = 'scale(0.9) translateY(20px)';
                            setTimeout(() => el.remove(), 500);
                        }
                    });
                }
            });
        }
    </script>
    @endsection
