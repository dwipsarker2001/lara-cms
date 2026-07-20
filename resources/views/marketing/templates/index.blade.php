    @extends('marketing.layouts.app')
    <title>ACCOUNT : Templates</title>

    @section('customStyle')
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @endsection

    @section('content')
    <div class="w-full min-h-screen px-8 py-10 bg-[#f8fafc]" x-data="{ activeTab: 'admin' }">

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 pb-6 border-b border-slate-200/60 max-w-[1600px] mx-auto">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight leading-none">Email Templates</h1>
                <p class="text-slate-500 font-medium mt-3 max-w-xl">Choose a high-converting layout or start from a wireframe.</p>
            </div>
            
            <div class="flex items-center border border-slate-200/80 gap-1.5 p-1 bg-slate-100 w-fit rounded-xl shadow-sm select-none shrink-0">
                <button @click="activeTab = 'admin'"
                    :class="activeTab === 'admin' ? 'bg-white text-[#007682] shadow-sm border border-slate-200/60' : 'text-slate-600 hover:text-slate-800'"
                    class="px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-300 active:scale-95 border-none cursor-pointer">
                    Default Templates
                </button>
                <button @click="activeTab = 'user'"
                    :class="activeTab === 'user' ? 'bg-white text-[#007682] shadow-sm border border-slate-200/60' : 'text-slate-600 hover:text-slate-800'"
                    class="px-5 py-2 text-sm font-semibold rounded-lg transition-all duration-300 active:scale-95 border-none cursor-pointer">
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

            <div x-show="activeTab === 'admin'" x-cloak x-transition.opacity.duration.400ms class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
                
                <!-- Blank Template Card -->
                <a href="{{ route('app.template.create') }}" class="group relative flex flex-col bg-white border border-slate-200 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_10px_20px_-5px_rgba(0,0,0,0.08)] no-underline">
                    <div class="relative h-[350px] flex flex-col items-center justify-center bg-slate-50/50 rounded-t-xl transition-all duration-300 p-6 text-center">
                        <div class="size-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 shadow-sm mb-4">
                            <i class="hgi hgi-stroke hgi-plus text-3xl"></i>
                        </div>
                        <span class="text-sm font-bold text-slate-800 transition-colors">Start from Scratch</span>
                        <p class="text-xs text-slate-400 mt-2 max-w-[200px] leading-relaxed">Create a customized email design from a completely blank canvas.</p>
                    </div>
                    <div class="px-5 py-3.5 bg-white border-t border-slate-100 flex items-center justify-between rounded-b-xl flex-1">
                        <div>
                            <h5 class="text-sm font-bold text-slate-900">Blank Template</h5>
                            <p class="text-slate-400 text-xs mt-0.5">Start fresh</p>
                        </div>
                        <div class="w-9 h-9 flex items-center justify-center bg-slate-50 group-hover:bg-slate-200 group-hover:text-slate-700 rounded-full text-slate-400 transition-colors shrink-0">
                            <i class="hgi hgi-stroke hgi-arrow-right-01 text-sm"></i>
                        </div>
                    </div>
                </a>
                @foreach ($defaultList as $template)
                    @php $template_id = $template->template_id; @endphp
                    <div class="group relative flex flex-col bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-[0_10px_20px_-5px_rgba(0,0,0,0.08)] transition-all duration-300 hover:-translate-y-1"
                        x-data="{ open: false }"
                        @click.outside="open = false"
                        :class="open ? 'z-40' : 'z-10'">
                        <div class="relative h-[350px] overflow-hidden bg-slate-100 flex items-center justify-center rounded-t-xl">
                            @if(file_exists(public_path("templates/user/" . $template_id . "/thumb.png")))
                                <div class="absolute inset-0 bg-cover bg-top transition-all duration-[4000ms] ease-in-out group-hover:bg-bottom"
                                    style="background-image: url('{{ asset('templates/user/'. $template_id. '/thumb.png') }}')">
                                </div>
                            @elseif(file_exists(public_path("templates/featured/" . $template_id . "/thumb.png")))
                                <div class="absolute inset-0 bg-cover bg-top transition-all duration-[4000ms] ease-in-out group-hover:bg-bottom"
                                    style="background-image: url('{{ asset('templates/featured/'. $template_id. '/thumb.png') }}')">
                                </div>
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200/50 flex flex-col p-6 justify-between select-none">
                                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                                        <div class="h-4 w-12 bg-slate-300/80 rounded"></div>
                                        <div class="flex gap-1.5">
                                            <div class="h-1.5 w-1.5 rounded-full bg-slate-300"></div>
                                            <div class="h-1.5 w-1.5 rounded-full bg-slate-300"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex flex-col justify-center gap-3 py-4">
                                        <div class="h-6 w-3/4 bg-gradient-to-r from-slate-300/90 to-slate-300/40 rounded-md"></div>
                                        <div class="space-y-1.5">
                                            <div class="h-2.5 w-full bg-slate-300/50 rounded"></div>
                                            <div class="h-2.5 w-5/6 bg-slate-300/50 rounded"></div>
                                            <div class="h-2.5 w-2/3 bg-slate-300/50 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 border-t border-slate-200/50 pt-3">
                                        <div class="h-7 w-20 bg-gradient-to-br from-[#007682]/70 to-[#408b86]/70 rounded-md shadow-sm"></div>
                                        <div class="h-2 w-16 bg-slate-300/40 rounded"></div>
                                    </div>
                                </div>
                            @endif
                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                <form method="post" action="{{ route('app.template.select') }}">
                                    @csrf
                                    <input name="id" value="{{ $template_id }}" hidden />
                                    <input name="type" value="admin" hidden />

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
                        <div class="px-5 py-3.5 bg-white border-t border-slate-50 relative flex items-center justify-between gap-2 rounded-b-xl">
                            <div class="min-w-0 flex-1">
                                <h5 class="text-sm font-bold text-slate-900 truncate">{{ $template->name }}</h5>
                                <p class="text-slate-400 text-xs mt-0.5">
                                    Default Template
                                </p>
                            </div>
                            
                            <!-- Ellipsis Dropdown -->
                            <div class="relative shrink-0">
                                <button @click.stop="open = !open" class="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-500 hover:text-slate-800 focus:outline-none flex items-center justify-center">
                                    <i class="hgi hgi-stroke hgi-more-horizontal text-xl leading-none"></i>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 top-full mt-1.5 w-40 bg-white border border-slate-200 rounded-xl shadow-lg z-50 py-1 origin-top-right">
                                    
                                    <!-- Use Template -->
                                    <form method="post" action="{{ route('app.template.select') }}" class="m-0">
                                        @csrf
                                        <input name="id" value="{{ $template_id }}" hidden />
                                        <input name="type" value="admin" hidden />
                                        <button type="submit"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-teal-600 transition-colors border-none bg-transparent cursor-pointer text-left">
                                            <i class="hgi hgi-stroke hgi-copy-01 text-sm"></i>
                                            Use Template
                                        </button>
                                    </form>

                                    <!-- Duplicate -->
                                    <button @click="open = false; duplicateTemplate('{{ $template_id }}')"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors text-left focus:outline-none border-none bg-transparent cursor-pointer">
                                        <i class="hgi hgi-stroke hgi-copy text-sm"></i>
                                        Duplicate
                                    </button>

                                    @if($template->user_id === auth()->id())
                                    <!-- Rename -->
                                    <button @click="open = false; renameTemplate('{{ $template_id }}', '{{ addslashes($template->name) }}')"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors text-left focus:outline-none border-none bg-transparent cursor-pointer">
                                        <i class="hgi hgi-stroke hgi-pencil text-sm"></i>
                                        Rename
                                    </button>

                                    <div class="h-px bg-slate-100 my-1"></div>

                                    <!-- Delete -->
                                    <button @click="open = false; removeTemplate('{{ $template_id }}')"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors text-left focus:outline-none border-none bg-transparent cursor-pointer">
                                        <i class="hgi hgi-stroke hgi-delete-02 text-sm"></i>
                                        Delete
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(count($mylist) != 0)
            <div x-show="activeTab === 'user'" x-cloak x-transition.opacity.duration.400ms class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
                @foreach ($mylist as $template)
                    @php $template_id = $template->template_id; @endphp
                    @if(file_exists(public_path("templates/user/" . $template_id . "/index.html")) || $template->content !== null)
                    <div id="template_card_{{ $template_id }}"
                        x-data="{ open: false }"
                        @click.outside="open = false"
                        :class="open ? 'z-40' : 'z-10'"
                        class="group relative flex flex-col bg-white border 
                        {{ session('badge') == $template_id ? 'border-teal-500 ring-4 ring-teal-50' : 'border-slate-200' }}
                        rounded-xl shadow-sm hover:shadow-[0_10px_20px_-5px_rgba(0,0,0,0.08)]
                        transition-all duration-300 hover:-translate-y-1">
                        <div class="relative h-[350px] overflow-hidden bg-slate-100 flex items-center justify-center rounded-t-xl">
                            @if(file_exists(public_path("templates/user/" . $template_id . "/thumb.png")))
                                <div class="absolute inset-0 bg-cover bg-top transition-all duration-[4000ms] ease-in-out group-hover:bg-bottom"
                                    style="background-image: url('{{ asset('templates/user/'. $template_id. '/thumb.png') }}')">
                                </div>
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200/50 flex flex-col p-6 justify-between select-none rounded-t-xl">
                                    <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                                        <div class="h-4 w-12 bg-slate-300/80 rounded"></div>
                                        <div class="flex gap-1.5">
                                            <div class="h-1.5 w-1.5 rounded-full bg-slate-300"></div>
                                            <div class="h-1.5 w-1.5 rounded-full bg-slate-300"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex flex-col justify-center gap-3 py-4">
                                        <div class="h-6 w-3/4 bg-gradient-to-r from-slate-300/90 to-slate-300/40 rounded-md"></div>
                                        <div class="space-y-1.5">
                                            <div class="h-2.5 w-full bg-slate-300/50 rounded"></div>
                                            <div class="h-2.5 w-5/6 bg-slate-300/50 rounded"></div>
                                            <div class="h-2.5 w-2/3 bg-slate-300/50 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 border-t border-slate-200/50 pt-3">
                                        <div class="h-7 w-20 bg-gradient-to-br from-[#007682]/70 to-[#408b86]/70 rounded-md shadow-sm"></div>
                                        <div class="h-2 w-16 bg-slate-300/40 rounded"></div>
                                    </div>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 
                                        transition-all duration-300 flex items-center justify-center 
                                        backdrop-blur-[2px]">
                                <a href="{{ route('app.template.design', ['id' => $template_id, 'type' => 'user']) }}"
                                    class="flex items-center gap-3 px-6 py-2.5 rounded-lg text-white font-semibold text-sm
                                    bg-gradient-to-br from-[#007682] to-[#408b86]
                                    hover:brightness-110 transition-all duration-300 active:scale-95 shadow-lg no-underline">
                                    <i class="hgi hgi-stroke hgi-edit-02 text-base"></i>
                                    <span>Edit Template</span>
                                </a>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="px-5 py-3.5 bg-white border-t border-slate-50 relative flex items-center justify-between gap-2 rounded-b-xl">
                            <div class="min-w-0 flex-1">
                                <h5 class="text-sm font-bold text-slate-900 truncate" id="template_name_text_{{ $template_id }}">
                                    {{ $template->name }}
                                </h5>
                                <p class="text-slate-400 text-xs mt-0.5">
                                    Updated {{ $template->created_at->diffForHumans() }}
                                </p>
                            </div>
                            
                            <!-- Ellipsis Dropdown -->
                            <div class="relative shrink-0">
                                <button @click.stop="open = !open" class="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-500 hover:text-slate-800 focus:outline-none flex items-center justify-center">
                                    <i class="hgi hgi-stroke hgi-more-horizontal text-xl leading-none"></i>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 top-full mt-1.5 w-40 bg-white border border-slate-200 rounded-xl shadow-lg z-50 py-1 origin-top-right">
                                    
                                    <!-- Edit -->
                                    <a href="{{ route('app.template.design', ['id' => $template_id, 'type' => 'user']) }}" 
                                       class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-teal-600 transition-colors no-underline">
                                        <i class="hgi hgi-stroke hgi-edit-02 text-sm"></i>
                                        Edit
                                    </a>
                                    
                                    <!-- Rename -->
                                    <button @click="open = false; renameTemplate('{{ $template_id }}', '{{ addslashes($template->name) }}')"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors text-left focus:outline-none">
                                        <i class="hgi hgi-stroke hgi-pencil text-sm"></i>
                                        Rename
                                    </button>
                                    
                                    <!-- Copy -->
                                    <button @click="open = false; duplicateTemplate('{{ $template_id }}')"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors text-left focus:outline-none">
                                        <i class="hgi hgi-stroke hgi-copy text-sm"></i>
                                        Copy
                                    </button>
                                    
                                    <div class="h-px bg-slate-100 my-1"></div>
                                    
                                    <!-- Delete -->
                                    <button @click="open = false; removeTemplate('{{ $template_id }}')"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors text-left focus:outline-none">
                                        <i class="hgi hgi-stroke hgi-delete-02 text-sm"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @else
            <div x-show="activeTab === 'user'" x-cloak class="flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-slate-200/80 shadow-sm max-w-xl mx-auto mt-10 p-8 text-center">
                <div class="size-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mb-4 border border-slate-100">
                    <i class="hgi hgi-stroke hgi-folder-open text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900">No saved templates yet</h3>
                <p class="text-slate-500 text-sm mt-2 max-w-sm">Create a new template from scratch or copy a default template from the Default Templates tab to get started.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Custom Delete Confirmation Modal -->
    <div id="customDeleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/30 backdrop-blur-sm p-4 transition-all duration-200">
        <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl text-center transform scale-95 opacity-0 transition-all duration-200" id="customDeleteModalContent">
            <div class="w-14 h-14 mx-auto flex items-center justify-center rounded-full bg-red-50 mb-4 border border-red-100">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mb-2">Delete Design?</h2>
            <p class="text-sm text-slate-500 mb-6 leading-relaxed">This template will be removed forever. This action cannot be undone.</p>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeCustomDeleteModal()" 
                        class="flex-1 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition-all cursor-pointer border-none">
                    Cancel
                </button>
                <button type="button" id="confirmDeleteBtn"
                        class="flex-1 py-2.5 rounded-xl bg-red-600 text-white text-sm font-bold hover:bg-red-700 transition-all cursor-pointer border-none">
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Rename Modal -->
    <div id="customRenameModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/30 backdrop-blur-sm p-4 transition-all duration-200">
        <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl transform scale-95 opacity-0 transition-all duration-200" id="customRenameModalContent">
            <h2 class="text-lg font-bold text-slate-900 mb-2">Rename Template</h2>
            <p class="text-sm text-slate-500 mb-4 leading-relaxed">Enter a new name for your template:</p>
            
            <div class="mb-6">
                <input type="text" id="renameInput"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-[#007682] focus:ring-1 focus:ring-[#007682] focus:outline-none text-sm text-slate-800 transition-all"
                       placeholder="Template Name" />
                <p id="renameError" class="hidden text-red-500 text-xs mt-1.5 font-semibold">Please enter a template name.</p>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeCustomRenameModal()" 
                        class="flex-1 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition-all cursor-pointer border-none">
                    Cancel
                </button>
                <button type="button" id="confirmRenameBtn"
                        class="flex-1 py-2.5 rounded-xl bg-[#007682] text-white text-sm font-bold hover:brightness-110 transition-all cursor-pointer border-none">
                    Rename
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Duplicate Confirmation Modal -->
    <div id="customDuplicateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/30 backdrop-blur-sm p-4 transition-all duration-200">
        <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl text-center transform scale-95 opacity-0 transition-all duration-200" id="customDuplicateModalContent">
            <div class="w-14 h-14 mx-auto flex items-center justify-center rounded-full bg-teal-50 mb-4 border border-teal-100">
                <i class="hgi hgi-stroke hgi-copy text-2xl text-[#007682]"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mb-2">Duplicate Template?</h2>
            <p class="text-sm text-slate-500 mb-6 leading-relaxed">Would you like to copy this template design to your list?</p>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeCustomDuplicateModal()" 
                        class="flex-1 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold hover:bg-slate-200 transition-all cursor-pointer border-none">
                    Cancel
                </button>
                <button type="button" id="confirmDuplicateBtn"
                        class="flex-1 py-2.5 rounded-xl bg-[#007682] text-white text-sm font-bold hover:brightness-110 transition-all cursor-pointer border-none">
                    Copy It
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Success Toast -->
    <div id="customSuccessToast" class="hidden fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-slate-900 text-white px-5 py-3.5 rounded-xl shadow-2xl transform translate-y-10 opacity-0 transition-all duration-300">
        <div class="size-6 rounded-full bg-emerald-500 flex items-center justify-center text-white shrink-0">
            <svg class="size-4 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <p class="text-sm font-semibold" id="successToastText">Success</p>
    </div>
    @endsection

    @section('script')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        let currentActiveTemplateId = null;

        // Custom modal helper functions with animations
        function openModal(modalId, contentId) {
            const modal = document.getElementById(modalId);
            const content = document.getElementById(contentId);
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeModal(modalId, contentId) {
            const modal = document.getElementById(modalId);
            const content = document.getElementById(contentId);
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 200);
        }

        // Show Success Toast
        function showSuccessToast(message) {
            const toast = document.getElementById('customSuccessToast');
            const text = document.getElementById('successToastText');
            text.textContent = message;
            
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            }, 10);

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.classList.add('hidden'), 300);
            }, 3000);
        }

        // --- Remove Template Modal ---
        function removeTemplate(id) {
            currentActiveTemplateId = id;
            openModal('customDeleteModal', 'customDeleteModalContent');
        }

        function closeCustomDeleteModal() {
            closeModal('customDeleteModal', 'customDeleteModalContent');
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!currentActiveTemplateId) return;
            const id = currentActiveTemplateId;
            closeCustomDeleteModal();

            $.ajax({
                url: "{{ route('app.template.remove') }}",
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                method: "post",
                data: { template_id: id },
                success: function() {
                    const el = document.getElementById("template_card_" + id);
                    if (el) {
                        el.style.opacity = '0';
                        el.style.transform = 'scale(0.9) translateY(20px)';
                        setTimeout(() => el.remove(), 500);
                    }
                    showSuccessToast("Template deleted successfully.");
                }
            });
        });

        // --- Rename Template Modal ---
        function renameTemplate(id, currentName) {
            currentActiveTemplateId = id;
            const input = document.getElementById('renameInput');
            input.value = currentName;
            document.getElementById('renameError').classList.add('hidden');
            input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            openModal('customRenameModal', 'customRenameModalContent');
            setTimeout(() => input.focus(), 100);
        }

        function closeCustomRenameModal() {
            closeModal('customRenameModal', 'customRenameModalContent');
        }

        document.getElementById('confirmRenameBtn').addEventListener('click', function() {
            if (!currentActiveTemplateId) return;
            const id = currentActiveTemplateId;
            const input = document.getElementById('renameInput');
            const newName = input.value.trim();
            const error = document.getElementById('renameError');

            if (!newName) {
                error.classList.remove('hidden');
                input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                return;
            }

            closeCustomRenameModal();

            $.ajax({
                url: "{{ route('app.template.rename') }}",
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                method: "post",
                data: { template_id: id, name: newName },
                success: function() {
                    const titleEl = document.getElementById("template_name_text_" + id);
                    if (titleEl) {
                        titleEl.textContent = newName;
                    }
                    showSuccessToast("Template renamed successfully.");
                }
            });
        });

        // --- Duplicate Template Modal ---
        function duplicateTemplate(id) {
            currentActiveTemplateId = id;
            openModal('customDuplicateModal', 'customDuplicateModalContent');
        }

        function closeCustomDuplicateModal() {
            closeModal('customDuplicateModal', 'customDuplicateModalContent');
        }

        document.getElementById('confirmDuplicateBtn').addEventListener('click', function() {
            if (!currentActiveTemplateId) return;
            const id = currentActiveTemplateId;
            closeCustomDuplicateModal();

            $.ajax({
                url: "{{ route('app.template.duplicate') }}",
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                method: "post",
                data: { template_id: id },
                success: function(response) {
                    if (response.success) {
                        showSuccessToast("Template duplicated successfully.");
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    }
                }
            });
        });

        // Handle Escape Key Close & Outside Click Close
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeCustomDeleteModal();
                closeCustomRenameModal();
                closeCustomDuplicateModal();
            }
        });

        // Outside Click handler for all modals
        ['customDeleteModal', 'customRenameModal', 'customDuplicateModal'].forEach(modalId => {
            const modal = document.getElementById(modalId);
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    if (modalId === 'customDeleteModal') closeCustomDeleteModal();
                    if (modalId === 'customRenameModal') closeCustomRenameModal();
                    if (modalId === 'customDuplicateModal') closeCustomDuplicateModal();
                }
            });
        });
    </script>
    @endsection
