@extends('marketing.layouts.app')

@section('content')
<div class="w-full min-h-screen bg-[#f8fafc] px-4 py-8 md:px-12">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Campaign Configuration</h1>
            <p class="mt-1 text-lg text-slate-600">{{$campaign->name}}</p>
        </div>
        <div class="flex items-center gap-4">
            <button type="button" onclick="publishCampaign()" 
                class="flex items-center gap-3 px-8 py-3 rounded-xl text-white bg-gradient-to-br from-[#007682] to-[#408b86] hover:brightness-110 transition-all duration-300 shadow-lg shadow-teal-900/10 active:scale-95">
                <i class="hgi hgi-stroke hgi-mail-send-01 text-lg"></i>
                <span class="font-bold text-sm uppercase tracking-widest text-nowrap">Publish Campaign</span>
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-7 space-y-6">
            <form method="POST" action="{{route('app.campaign.update')}}" id="campaign-form">
                @csrf
                <input type="hidden" name="id" value="{{$campaign->id}}">
                <input type="hidden" name="name" value="{{$campaign->name}}">
                <input type="hidden" name="action" id="form-action" value="campaign">
                <input type="hidden" name="template_id" id="template_id" value="{{$campaign->template_id}}">
                
                <!-- Sender Identity Section -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
                    <div class="px-8 py-4 bg-slate-50 border-b flex justify-between items-center">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <i class="hgi hgi-stroke hgi-user text-teal-600"></i> Sender Identity
                        </h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 ml-1 block mb-2 uppercase tracking-wider">Display Name</label>
                            <input type="text" id="from_name" name="from_name" value="{{$campaign->from_name}}" onkeyup="updateLivePreview()" required
                                    placeholder="Username or Brand Name"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">From Email</label>
                            <input type="email" id="from_email" name="from_email" value="{{$campaign->from_email}}" onkeyup="updateLivePreview()" required
                                    placeholder="example@yourcompany.com"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                        </div>
                    </div>
                </div>

                <!-- Audience Segment Section -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
                    <div class="px-8 py-4 bg-slate-50 border-b flex justify-between items-center">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <i class="hgi hgi-stroke hgi-user-multiple-02 text-teal-600"></i> Audience Segment
                        </h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">Add contact group</label>
                            <select id="contacts" onchange="selectGroup()" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm">
                                <option value="select">Choose a group...</option>
                                @foreach($groups as $group)
                                    <option value="{{$group->id}}">{{$group->name}} ({{$group->count}})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <input name="receiver_emails" id="receiver_emails" value="{{$campaign->receiver_emails}}" type="hidden"/>
                        
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-3 tracking-wider">Active Group Lists</label>
                            <div id="groups_list" class="flex flex-wrap gap-2">
                                @foreach($initialGroupList as $initialGroup)
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-br from-[#007682] to-[#408b86] shadow-sm rounded-lg text-[10px] font-black uppercase tracking-tighter border border-[#007682]/30">
                                    <span class="text-white">{{$initialGroup->label}}</span>
                                    <button type="button" onclick="removeGroup('{{$initialGroup->id}}')" class="text-white/60 hover:text-red-300 transition-colors">
                                        <i class="hgi hgi-stroke hgi-cancel-01"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subject & Preview Section -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
                    <div class="px-8 py-4 bg-slate-50 border-b flex justify-between items-center">
                        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                            <i class="hgi hgi-stroke hgi-mail-02 text-teal-600"></i> Subject & Preview
                        </h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">Subject Line</label>
                            <input type="text" id="subject_line" name="subject_line" value="{{$campaign->subject_line}}" onkeyup="updateLivePreview()" required
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm" placeholder="Enter a catchy subject...">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase ml-1 block mb-2 tracking-wider">Preview Text</label>
                            <textarea id="preview_text" name="preview_text" onkeyup="updateLivePreview()" rows="2"
                                      class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-teal-400 outline-none transition font-bold text-slate-700 text-sm resize-none" placeholder="Brief summary for the inbox...">{{$campaign->preview_text}}</textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Sidebar -->
        <div class="lg:col-span-5 lg:sticky lg:top-8 space-y-6">
            <!-- Inbox Live Preview -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 border-b px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                    </div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Inbox Live Preview</span>
                </div>

                <div class="flex bg-white">
                    <div class="w-16 border-r flex flex-col items-center py-4 gap-6">
                        <i class="hgi hgi-stroke hgi-mail-01 text-teal-600 text-xl"></i>
                        <i class="hgi hgi-stroke hgi-star text-slate-400 text-xl"></i>
                        <i class="hgi hgi-stroke hgi-clock-01 text-slate-400 text-lg"></i>
                        <i class="hgi hgi-stroke hgi-send-01 text-slate-400 text-lg"></i>
                    </div>

                    <div class="flex-1 overflow-hidden">
                        <div class="h-10 border-b border-slate-100 flex items-center px-4 gap-4">
                            <input type="checkbox" class="rounded border-slate-300 pointer-events-none">
                            <i class="hgi hgi-stroke hgi-refresh-01 text-slate-300 text-xs"></i>
                            <i class="hgi hgi-stroke hgi-more-vertical text-slate-300 text-xs ml-auto"></i>
                        </div>

                        <div class="flex items-center px-4 py-3 border-b border-slate-100 bg-teal-50/30 group transition-all">
                            <div class="flex items-center gap-3 w-full min-w-0">
                                <i class="hgi hgi-stroke hgi-star-01 text-slate-300 text-xs flex-shrink-0"></i>
                                
                                <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-4 w-full min-w-0">
                                    <span id="preview_from" class="text-sm font-medium text-slate-900 truncate w-32 flex-shrink-0 leading-tight">
                                        {{ $campaign->from_name ?: 'Your Name' }}
                                    </span>

                                    <div class="flex-1 truncate text-sm leading-tight">
                                        <span id="preview_subject" class="font-medium text-slate-900">
                                            {{ $campaign->subject_line ?: '(No Subject)' }}
                                        </span>
                                        <span class="text-slate-400 mx-1">—</span>
                                        <span id="preview_body" class="text-slate-500 font-medium">
                                            {{ $campaign->preview_text ?: 'Preview text will appear here...' }}
                                        </span>
                                    </div>

                                    <span class="text-[10px] font-bold text-slate-400 ml-auto flex-shrink-0">9:41 AM</span>
                                </div>
                            </div>
                        </div>

                        @for($i=0; $i<3; $i++)
                        <div class="flex items-center px-4 py-3 border-b border-slate-50 opacity-30 grayscale">
                            <div class="flex items-center gap-3 w-full min-w-0">
                                <i class="hgi hgi-stroke hgi-star-01 text-slate-200 text-xs"></i>
                                <div class="w-24 h-3 bg-slate-100 rounded"></div>
                                <div class="w-full h-3 bg-slate-50 rounded"></div>
                                <div class="w-8 h-2 bg-slate-50 rounded"></div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                <div class="p-4 bg-slate-50 text-center border-t border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">
                        This is how your email appears in the <br> <span class="text-slate-600 font-black">Gmail Desktop Inbox</span>
                    </p>
                </div>
            </div>

            <!-- Visual Layout Section -->
            @php
                // Determine if the template is valid/available
                // A template is "active" only if:
                // 1. It's a predefined template (template_id < 0), OR
                // 2. It's a custom template (template_id > 0) AND the template relation still exists
                $isTemplateActive = $campaign->template_id != 0 && (
                    $campaign->template_id < 0 || 
                    ($campaign->template_id > 0 && $campaign->template !== null)
                );

                // Build the thumb path only when template is truly active
                if ($isTemplateActive) {
                    $thumbPath = $campaign->template_id < 0
                        ? 'predefined' . $campaign->template_id
                        : $campaign->template->template_id;
                } else {
                    $thumbPath = null;
                    // Reset template_id on the object so the rest of the view treats it as "not selected"
                    // Note: this does NOT save to DB, it's only for rendering this page
                    $campaign->template_id = 0;
                }
            @endphp

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden group transition-all duration-300 hover:border-teal-200">
                <div class="px-8 py-4 bg-slate-50 border-b flex justify-between items-center">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                        <i class="hgi hgi-stroke hgi-paint-board text-teal-600"></i> Visual Layout
                    </h3>
                    <div class="flex items-center gap-1.5">
                        <div id="template-status-dot" class="w-1.5 h-1.5 rounded-full {{ $isTemplateActive ? 'bg-teal-500' : 'bg-slate-300' }}"></div>
                        <span id="template-status-labe" class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">
                            {{ $isTemplateActive ? 'Template Active' : 'Not Selected' }}
                        </span>
                    </div>
                </div>

                <div class="p-8">
                    <div id="template-preview" class="relative aspect-[16/10] bg-slate-50 rounded-xl border-2 border-dashed border-slate-100 mb-6 flex items-center justify-center overflow-hidden transition-colors">
                        @if($isTemplateActive && $thumbPath)
                            <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                                src="{{asset('public/templates/user/'. $thumbPath. '/thumb.png')}}"/>
                            <div class="absolute inset-0 bg-teal-900/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        @else
                            <div class="text-center">
                                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm border border-slate-50">
                                    <i class="hgi hgi-stroke hgi-image-01 text-slate-300 text-xl"></i>
                                </div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">
                                    No layout <br> chosen yet
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="button" onclick="openTemplateModal()" 
                            class="w-full py-3.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300
                            {{ !$isTemplateActive
                                ? 'bg-slate-900 text-white hover:bg-teal-700 shadow-lg shadow-slate-200' 
                                : 'bg-white border border-slate-200 text-slate-600 hover:border-teal-400 hover:text-teal-600' }}">
                            
                            <i class="hgi hgi-stroke {{ !$isTemplateActive ? 'hgi-mail-add-02' : 'hgi-pencil-edit-02' }} mr-2"></i>
                            {{ !$isTemplateActive ? 'Select Design' : 'Change Template' }}
                        </button>
                        
                        @if($isTemplateActive)
                            <p id="template-id-display" class="text-center text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                Template ID: #{{ abs($campaign->template_id) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- -------------------------------------------------
            Template Selection Modal 
------------------------------------------------------>
<div id="templateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden animate-modal">
        <!-- Modal Header -->
        <div class="px-8 py-6 bg-gradient-to-br from-slate-50 to-white border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">Choose Your Template</h2>
                <p class="text-sm text-slate-500 mt-1">Select a design that matches your campaign style</p>
            </div>
            <button type="button" onclick="closeTemplateModal()" class="w-10 h-10 rounded-full hover:bg-slate-100 transition-colors flex items-center justify-center">
                <i class="hgi hgi-stroke hgi-cancel-01 text-slate-400 text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="overflow-y-auto max-h-[calc(90vh-180px)]">
            <!-- Tabs -->
            <div class="flex gap-2 mb-6 border-b border-slate-200">
                <button type="button" onclick="switchTab('predefined')" id="tab-predefined" class="tab-button active px-6 py-3 text-sm font-bold uppercase tracking-wider transition-all border-b-2 border-teal-500 text-teal-600">
                    <i class="hgi hgi-stroke hgi-grid-view mr-2"></i>
                    Predefined Templates
                </button>
                <button type="button" onclick="switchTab('custom')" id="tab-custom" class="tab-button px-6 py-3 text-sm font-bold uppercase tracking-wider transition-all border-b-2 border-transparent text-slate-500 hover:text-slate-700">
                    <i class="hgi hgi-stroke hgi-paint-board mr-2"></i>
                    My Custom Templates
                </button>
            </div>

            <!-- Predefined Templates Grid -->
            <div id="predefined-templates" class="template-tab-content px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @for($i = -1; $i >= -6; $i--)
                    <div class="template-card group cursor-pointer border-2 rounded-xl overflow-hidden hover:border-teal-500 hover:shadow-xl transition-all duration-300 {{ $campaign->template_id == $i ? 'border-teal-500 ring-4 ring-teal-500/20' : 'border-slate-200' }}" 
                         data-template-db-id="{{ $i }}"
                         data-template-unique-id="predefined{{ $i }}"
                         onclick="selectTemplate({{ $i }}, 'predefined{{ $i }}')">
                        <div class="aspect-[16/10] bg-slate-100 overflow-hidden relative">
                            <img src="{{asset('public/templates/user/predefined'.$i.'/thumb.png')}}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" 
                                 onerror="this.src='{{asset('public/assets/img/template.png')}}'">
                            @if($campaign->template_id == $i)
                            <div class="absolute top-3 right-3 bg-teal-500 text-white px-3 py-1 rounded-full text-xs font-bold uppercase flex items-center gap-1">
                                <i class="hgi hgi-stroke hgi-tick-02"></i>Active
                            </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        <div class="p-4 bg-white">
                            <h3 class="font-bold text-slate-900 text-sm">Template {{ abs($i) }}</h3>
                            <p class="text-xs text-slate-500 mt-1">Predefined Design</p>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            <!-- Custom Templates Grid -->
            <div id="custom-templates" class="template-tab-content hidden px-6 pb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if(isset($mylist) && count($mylist) > 0)
                        @foreach($mylist as $template)
                        @php 
                            $template_db_id = $template->id; 
                            $template_unique_id = $template->template_id; 
                        @endphp
                        @if(file_exists(public_path("templates/user/" . $template_unique_id . "/thumb.png")))
                        <div class="template-card group cursor-pointer border-2 rounded-xl overflow-hidden hover:border-teal-500 hover:shadow-xl transition-all duration-300 {{ $campaign->template_id == $template_db_id ? 'border-teal-500 ring-4 ring-teal-500/20' : 'border-slate-200' }}" 
                             data-template-db-id="{{ $template_db_id }}"
                             data-template-unique-id="{{ $template_unique_id }}"
                             onclick="selectTemplate({{ $template_db_id }}, '{{ $template_unique_id }}')">
                            <div class="aspect-[16/10] bg-slate-100 overflow-hidden relative">
                                <img src="{{ asset('public/templates/user/'. $template_unique_id. '/thumb.png') }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                     onerror="this.src='{{asset('public/assets/img/template.png')}}'">
                                @if($campaign->template_id == $template_db_id)
                                <div class="absolute top-3 right-3 bg-teal-500 text-white px-3 py-1 rounded-full text-xs font-bold uppercase flex items-center gap-1">
                                    <i class="hgi hgi-stroke hgi-tick-02"></i>Active
                                </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="p-4 bg-white">
                                <h3 class="font-bold text-slate-900 text-sm">{{ $template->name }}</h3>
                                <p class="text-xs text-slate-500 mt-1">Custom Design • ID: {{ $template_unique_id }}</p>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    @else
                        <div class="col-span-full text-center py-12">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="hgi hgi-stroke hgi-folder-open text-slate-300 text-2xl"></i>
                            </div>
                            <p class="text-slate-400 font-bold text-sm uppercase tracking-wider">No custom templates yet</p>
                            <p class="text-slate-400 text-xs mt-2">Create your first custom template to see it here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-slate-50 py-4 px-8 border-t border-slate-200 flex items-center justify-between">
            <button type="button" onclick="closeTemplateModal()" class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-sm uppercase tracking-wider hover:bg-slate-100 transition-all">
                Cancel
            </button>
            <button type="button" onclick="applyTemplate()" class="px-8 py-3 rounded-xl bg-gradient-to-br from-[#007682] to-[#408b86] text-white font-bold text-sm uppercase tracking-wider hover:brightness-110 transition-all shadow-lg">
                <i class="hgi hgi-stroke hgi-tick-02 mr-2"></i>
                Apply Template
            </button>
        </div>
    </div>
</div>


<style>
@keyframes modal-in {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-modal {
    animation: modal-in 0.2s ease-out;
}

.tab-button.active {
    border-color: #14b8a6;
    color: #14b8a6;
}

.tab-button:not(.active) {
    color: #64748b;
}

.tab-button:not(.active):hover {
    color: #334155;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Global variables to track template selection
    let selectedTemplateDBID = {{ $campaign->template_id }}; // This is what gets saved to database
    let selectedTemplateUniqueID = ""; // This is used for file paths
    let selectedTemplateImage = "";

    // Real-time Inbox Update
    function updateLivePreview() {
        const from = document.getElementById('from_name').value;
        const subject = document.getElementById('subject_line').value;
        const preview = document.getElementById('preview_text').value;

        document.getElementById('preview_from').innerText = from || 'Your Name';
        document.getElementById('preview_subject').innerText = subject || '(No Subject)';
        document.getElementById('preview_body').innerText = preview || 'Preview text will appear here...';
    }

    // Group Selection Logic
    function selectGroup() {
        const id = $("#contacts").val();
        if(id === 'select') return;
        const label = $("#contacts option:selected").text();
        let list = JSON.parse($("#receiver_emails").val() || "[]");
        if(list.some(i => i.id == id)) {
            alert("Contact group is already added.");
            $("#contacts").val('select');
            return;
        }

        list.push({id, label});
        $("#receiver_emails").val(JSON.stringify(list));
        $("#contacts").val('select');
        updateGroupList();
    }

    function removeGroup(id) {
        let list = JSON.parse($("#receiver_emails").val());
        list = list.filter(i => i.id != id);
        $("#receiver_emails").val(JSON.stringify(list));
        updateGroupList();
    }

    function updateGroupList() {
        var list = JSON.parse($("#receiver_emails").val() || "[]");
        let html = "";
        list.forEach(item => {
            html += `
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-br from-[#007682] to-[#408b86] shadow-sm rounded-lg text-[10px] text-white font-black uppercase border border-[#007682]/30">
                    ${item.label}
                    <button type="button" onclick="removeGroup('${item.id}')" class="text-white/60 hover:text-red-300 transition-colors">
                        <i class="hgi hgi-stroke hgi-cancel-01"></i>
                    </button>
                </div>`;
        });
        
        if(html === "") {
            html = '<p class="text-slate-400 text-xs font-medium">No groups selected yet</p>';
        }
        
        $("#groups_list").html(html);
    }

    // Publish Campaign Function
    function publishCampaign() {
        document.getElementById('form-action').value = 'campaign';
        document.getElementById('campaign-form').submit();
    }

    // Template Modal Functions
    function openTemplateModal() {
        const modal = document.getElementById('templateModal');
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        return false;
    }

    function closeTemplateModal() {
        const modal = document.getElementById('templateModal');
        modal.style.display = 'none';
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function switchTab(tab) {
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active', 'border-teal-500', 'text-teal-600');
            btn.classList.add('text-slate-500');
        });
        
        const activeTab = document.getElementById('tab-' + tab);
        activeTab.classList.add('active', 'border-teal-500', 'text-teal-600');
        activeTab.classList.remove('text-slate-500');
        
        document.querySelectorAll('.template-tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById(tab + '-templates').classList.remove('hidden');
    }

    /**
     * Select a template
     * @param {number} dbID - The database ID (what gets saved to DB)
     * @param {string} uniqueID - The unique template ID (used for file paths)
     */
    function selectTemplate(dbID, uniqueID) {
        console.log("Selecting template - DB ID:", dbID, "Unique ID:", uniqueID);
        
        // Update global variables
        selectedTemplateDBID = dbID;
        selectedTemplateUniqueID = uniqueID;
        
        // Remove selection from all cards
        document.querySelectorAll('.template-card').forEach(card => {
            card.classList.remove('border-teal-500', 'ring-4', 'ring-teal-500/20');
            card.classList.add('border-slate-200');
            
            // Remove active badge if exists
            const activeBadge = card.querySelector('.absolute.top-3.right-3');
            if (activeBadge) {
                activeBadge.remove();
            }
        });
        
        // Add selection to clicked card using DB ID
        const selectedCard = document.querySelector(`.template-card[data-template-db-id="${dbID}"]`);
        if (selectedCard) {
            selectedCard.classList.remove('border-slate-200');
            selectedCard.classList.add('border-teal-500', 'ring-4', 'ring-teal-500/20');
            
            // Add active badge
            const imageContainer = selectedCard.querySelector('.aspect-\\[16\\/10\\]');
            if (imageContainer && !imageContainer.querySelector('.absolute.top-3.right-3')) {
                const badge = document.createElement('div');
                badge.className = 'absolute top-3 right-3 bg-teal-500 text-white px-3 py-1 rounded-full text-xs font-bold uppercase flex items-center gap-1';
                badge.innerHTML = '<i class="hgi hgi-stroke hgi-tick-02"></i>Active';
                imageContainer.appendChild(badge);
            }
            
            // Store the image src
            const img = selectedCard.querySelector('img');
            if (img) {
                selectedTemplateImage = img.src;
            }
        }
    }

    function applyTemplate() {
        if (!selectedTemplateDBID && selectedTemplateDBID !== 0) {
            alert('Please select a template first');
            return;
        }

        const selectedCard = document.querySelector(`.template-card[data-template-db-id="${selectedTemplateDBID}"]`);
        if (!selectedCard) {
            console.error("Template card not found");
            return;
        }

        const img = selectedCard.querySelector('img');
        if (img) {
            selectedTemplateImage = img.src;
        }

        // Update the preview div permanently
        const previewDiv = document.getElementById('template-preview');
        if (previewDiv) {
            previewDiv.innerHTML = `
                <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="${selectedTemplateImage}" alt="Template Preview" />
                <div class="absolute inset-0 bg-teal-900/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            `;
        }

        // Update hidden input
        const templateInput = document.getElementById('template_id');
        if (templateInput) {
            templateInput.value = selectedTemplateDBID;
        }

        // Update template ID display
        const templateIdDisplay = document.getElementById('template-id-display');
        if (templateIdDisplay) {
            templateIdDisplay.textContent = `Template ID: #${Math.abs(selectedTemplateDBID)}`;
        } else if (selectedTemplateDBID !== 0) {
            const buttonContainer = document.querySelector('.flex.flex-col.gap-3');
            if (buttonContainer) {
                const newDisplay = document.createElement('p');
                newDisplay.id = 'template-id-display';
                newDisplay.className = 'text-center text-[9px] font-bold text-slate-400 uppercase tracking-wider';
                newDisplay.textContent = `Template ID: #${Math.abs(selectedTemplateDBID)}`;
                buttonContainer.appendChild(newDisplay);
            }
        }

        // Update status dot and label to "Active"
        const statusDot = document.getElementById('template-status-dot');
        const statusLabel = document.getElementById('template-status-label');
        if (statusDot) {
            statusDot.classList.remove('bg-slate-300');
            statusDot.classList.add('bg-teal-500');
        }
        if (statusLabel) {
            statusLabel.textContent = 'Template Active';
        }

        // Update the Select/Change button text and icon
        const selectBtn = document.querySelector('[onclick="openTemplateModal()"]');
        if (selectBtn) {
            selectBtn.classList.remove('bg-slate-900', 'text-white', 'shadow-lg', 'shadow-slate-200', 'hover:bg-teal-700');
            selectBtn.classList.add('bg-white', 'border', 'border-slate-200', 'text-slate-600', 'hover:border-teal-400', 'hover:text-teal-600');
            selectBtn.innerHTML = `<i class="hgi hgi-stroke hgi-pencil-edit-02 mr-2"></i>Change Template`;
        }

        closeTemplateModal();
    }

    // Close modal on outside click
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('templateModal');
        if(modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeTemplateModal();
                }
            });
        }
    });

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeTemplateModal();
        }
    });
</script>
@endsection
