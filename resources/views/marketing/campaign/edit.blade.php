@extends('marketing.layouts.app')

@section('content')
<div class="w-full min-h-screen bg-slate-50/50 p-6 text-slate-900">
    <div class="mx-auto">
        <!-- Top Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Campaign Configuration
                </h1>
                <p class="text-slate-500 text-sm mt-1">{{ $campaign->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="publishCampaign()" 
                    class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-white bg-gradient-to-r from-[#007682] to-[#408b86] hover:brightness-105 transition-all duration-200 shadow-sm active:scale-95 cursor-pointer">
                    <i class="hgi hgi-stroke hgi-mail-send-01 text-base"></i>
                    <span class="font-bold text-xs">Publish Campaign</span>
                </button>
            </div>
        </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Main Form Column -->
        <div class="lg:col-span-7 space-y-5">
            <form method="POST" action="{{route('app.campaign.update')}}" id="campaign-form">
                @csrf
                <input type="hidden" name="id" value="{{$campaign->id}}">
                <input type="hidden" name="name" value="{{$campaign->name}}">
                <input type="hidden" name="action" id="form-action" value="campaign">
                <input type="hidden" name="template_id" id="template_id" value="{{$campaign->template_id}}">
                
                <!-- Sender Identity Section -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-5">
                    <div class="px-6 py-3.5 bg-slate-50/70 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-xs font-bold text-slate-800 flex items-center gap-2">
                            <i class="hgi hgi-stroke hgi-user text-[#007682] text-sm"></i> Sender Identity
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="text-xs font-semibold text-slate-700 block mb-1.5">Display Name</label>
                            <input type="text" id="from_name" name="from_name" value="{{$campaign->from_name}}" onkeyup="updateLivePreview()" required
                                    placeholder="Username or Brand Name"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-[#007682] focus:ring-2 focus:ring-[#007682]/10 outline-none transition font-medium text-slate-800 text-xs">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-700 block mb-1.5">From Email</label>
                            <input type="email" id="from_email" name="from_email" value="{{$campaign->from_email}}" onkeyup="updateLivePreview()" required
                                    placeholder="example@yourcompany.com"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-[#007682] focus:ring-2 focus:ring-[#007682]/10 outline-none transition font-medium text-slate-800 text-xs">
                        </div>
                    </div>
                </div>

                <!-- Audience Segment Section -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-5">
                    <div class="px-6 py-3.5 bg-slate-50/70 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-xs font-bold text-slate-800 flex items-center gap-2">
                            <i class="hgi hgi-stroke hgi-user-multiple-02 text-[#007682] text-sm"></i> Audience Segment
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="text-xs font-semibold text-slate-700 block mb-1.5">Add Contact Group</label>
                            <select id="contacts" onchange="selectGroup()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-[#007682] focus:ring-2 focus:ring-[#007682]/10 outline-none transition font-medium text-slate-800 text-xs">
                                <option value="select">Choose a group...</option>
                                @foreach($groups as $group)
                                    <option value="{{$group->id}}">{{$group->name}} ({{$group->count}})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <input name="receiver_emails" id="receiver_emails" value="{{$campaign->receiver_emails}}" type="hidden"/>
                        
                        <div>
                            <label class="text-xs font-semibold text-slate-700 block mb-2">Active Group Lists</label>
                            <div id="groups_list" class="flex flex-wrap gap-2">
                                @foreach($initialGroupList as $initialGroup)
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 border border-slate-200/80 rounded-lg text-xs font-medium text-slate-700">
                                    <span>{{$initialGroup->label}}</span>
                                    <button type="button" onclick="removeGroup('{{$initialGroup->id}}')" class="text-slate-400 hover:text-red-500 transition-colors">
                                        <i class="hgi hgi-stroke hgi-cancel-01 text-xs"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subject & Preview Section -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-5">
                    <div class="px-6 py-3.5 bg-slate-50/70 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-xs font-bold text-slate-800 flex items-center gap-2">
                            <i class="hgi hgi-stroke hgi-mail-02 text-[#007682] text-sm"></i> Subject & Preview
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="text-xs font-semibold text-slate-700 block mb-1.5">Subject Line</label>
                            <input type="text" id="subject_line" name="subject_line" value="{{$campaign->subject_line}}" onkeyup="updateLivePreview()" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-[#007682] focus:ring-2 focus:ring-[#007682]/10 outline-none transition font-medium text-slate-800 text-xs" placeholder="Enter a catchy subject...">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-700 block mb-1.5">Preview Text</label>
                            <textarea id="preview_text" name="preview_text" onkeyup="updateLivePreview()" rows="2"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/30 focus:bg-white focus:border-[#007682] focus:ring-2 focus:ring-[#007682]/10 outline-none transition font-medium text-slate-800 text-xs resize-none" placeholder="Brief summary for the inbox...">{{$campaign->preview_text}}</textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Sidebar -->
        <div class="lg:col-span-5 lg:sticky lg:top-6 space-y-4">
            <!-- Inbox Live Preview -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50/80 border-b border-slate-100 px-4 py-2.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-amber-400"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-400"></div>
                    </div>
                    <span class="text-xs font-semibold text-slate-600">Inbox Live Preview</span>
                </div>

                <div class="flex bg-white">
                    <div class="w-10 border-r border-slate-100 flex flex-col items-center py-3 gap-4 text-slate-400">
                        <i class="hgi hgi-stroke hgi-mail-01 text-[#007682] text-sm"></i>
                        <i class="hgi hgi-stroke hgi-star text-slate-300 text-sm"></i>
                        <i class="hgi hgi-stroke hgi-clock-01 text-slate-300 text-sm"></i>
                        <i class="hgi hgi-stroke hgi-send-01 text-slate-300 text-sm"></i>
                    </div>

                    <div class="flex-1 overflow-hidden">
                        <div class="h-8 border-b border-slate-100 flex items-center px-3 gap-3">
                            <input type="checkbox" class="rounded border-slate-300 pointer-events-none">
                            <i class="hgi hgi-stroke hgi-refresh-01 text-slate-300 text-xs"></i>
                            <i class="hgi hgi-stroke hgi-more-vertical text-slate-300 text-xs ml-auto"></i>
                        </div>

                        <div class="flex items-center px-3 py-2 border-b border-slate-100 bg-teal-50/30 transition-all">
                            <div class="flex items-center gap-2.5 w-full min-w-0">
                                <i class="hgi hgi-stroke hgi-star-01 text-slate-300 text-xs flex-shrink-0"></i>
                                
                                <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-3 w-full min-w-0">
                                    <span id="preview_from" class="text-xs font-semibold text-slate-900 truncate w-24 flex-shrink-0 leading-tight">
                                        {{ $campaign->from_name ?: 'Your Name' }}
                                    </span>

                                    <div class="flex-1 truncate text-xs leading-tight">
                                        <span id="preview_subject" class="font-semibold text-slate-800">
                                            {{ $campaign->subject_line ?: '(No Subject)' }}
                                        </span>
                                        <span class="text-slate-400 mx-1">—</span>
                                        <span id="preview_body" class="text-slate-500 font-normal">
                                            {{ $campaign->preview_text ?: 'Preview text will appear here...' }}
                                        </span>
                                    </div>

                                    <span class="text-[10px] font-medium text-slate-400 ml-auto flex-shrink-0">9:41 AM</span>
                                </div>
                            </div>
                        </div>

                        @for($i=0; $i<1; $i++)
                        <div class="flex items-center px-3 py-2 border-b border-slate-50 opacity-25 grayscale">
                            <div class="flex items-center gap-2.5 w-full min-w-0">
                                <i class="hgi hgi-stroke hgi-star-01 text-slate-200 text-xs"></i>
                                <div class="w-16 h-2 bg-slate-100 rounded"></div>
                                <div class="w-full h-2 bg-slate-50 rounded"></div>
                                <div class="w-5 h-2 bg-slate-50 rounded"></div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                <div class="px-3 py-2 bg-slate-50/60 text-center border-t border-slate-100">
                    <p class="text-[11px] font-medium text-slate-500">
                        Gmail Desktop Inbox Preview
                    </p>
                </div>
            </div>

            <!-- Visual Layout Section -->
            @php
                $isTemplateActive = $campaign->template_id != 0 && $campaign->template !== null;
                if (! $isTemplateActive) {
                    $campaign->template_id = 0;
                }
            @endphp

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden group transition-all duration-200 hover:border-slate-300">
                <div class="px-4 py-2.5 bg-slate-50/80 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xs font-bold text-slate-800 flex items-center gap-2">
                        <i class="hgi hgi-stroke hgi-paint-board text-[#007682] text-sm"></i> Visual Layout
                    </h3>
                    <div class="flex items-center gap-1.5">
                        <div id="template-status-dot" class="w-2 h-2 rounded-full {{ $isTemplateActive ? 'bg-[#007682]' : 'bg-slate-300' }}"></div>
                        <span id="template-status-label" class="text-xs font-medium text-slate-500">
                            {{ $isTemplateActive ? 'Template Active' : 'Not Selected' }}
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <div id="template-preview" class="relative h-40 bg-slate-50 rounded-lg border border-dashed border-slate-200 mb-4 flex items-center justify-center overflow-hidden transition-colors">
                        @if($isTemplateActive)
                            @php
                                $tpl = $campaign->template;
                                $gradients = ['from-teal-500 to-teal-600', 'from-blue-500 to-indigo-600', 'from-purple-500 to-pink-600'];
                                $g = $gradients[crc32($tpl->template_id) % count($gradients)];
                            @endphp
                            <div class="w-full h-full bg-gradient-to-br {{ $g }} flex items-center justify-center">
                                <div class="text-center">
                                    <span class="text-4xl font-bold text-white/20 select-none block">{{ strtoupper(substr($tpl->name, 0, 1)) }}</span>
                                    <span class="text-white/90 text-xs font-semibold mt-1 block">{{ $tpl->name }}</span>
                                </div>
                            </div>
                        @else
                            <div class="text-center">
                                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mx-auto mb-2 shadow-xs border border-slate-100">
                                    <i class="hgi hgi-stroke hgi-image-01 text-slate-400 text-lg"></i>
                                </div>
                                <p class="text-xs font-medium text-slate-400">
                                    No layout chosen yet
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="button" onclick="openTemplateModal()" 
                            class="w-full py-2.5 rounded-lg text-xs font-semibold transition-all duration-200 cursor-pointer
                            {{ !$isTemplateActive
                                ? 'bg-slate-900 text-white hover:bg-[#007682] shadow-xs' 
                                : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                            
                            <i class="hgi hgi-stroke {{ !$isTemplateActive ? 'hgi-mail-add-02' : 'hgi-pencil-edit-02' }} mr-1.5 text-xs"></i>
                            {{ !$isTemplateActive ? 'Select Design' : 'Change Template' }}
                        </button>
                        
                        @if($isTemplateActive)
                            <p id="template-id-display" class="text-center text-[10px] font-medium text-slate-400">
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
<div id="templateModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4" style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[85vh] flex flex-col overflow-hidden animate-modal border border-slate-100">
        <!-- Modal Header -->
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center text-[#007682]">
                    <i class="hgi hgi-stroke hgi-layout-01 text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 tracking-tight">Choose Email Template</h2>
                    <p class="text-[11px] text-slate-500">Select a layout for your campaign</p>
                </div>
            </div>
            <button type="button" onclick="closeTemplateModal()" class="w-7 h-7 rounded-full hover:bg-slate-200/60 transition-colors flex items-center justify-center text-slate-400 hover:text-slate-600">
                <i class="hgi hgi-stroke hgi-cancel-01 text-base"></i>
            </button>
        </div>

        <!-- Subheader / Segmented Tabs -->
        <div class="px-5 py-2.5 bg-white border-b border-slate-100 flex items-center justify-between flex-shrink-0">
            <div class="inline-flex bg-slate-100 p-1 rounded-xl gap-1">
                <button type="button" onclick="switchTab('predefined')" id="tab-predefined" class="tab-button active px-3 py-1.5 rounded-lg text-xs font-semibold tracking-wide transition-all text-[#007682] bg-white shadow-xs">
                    <i class="hgi hgi-stroke hgi-grid-view mr-1.5"></i>
                    Predefined
                </button>
                <button type="button" onclick="switchTab('custom')" id="tab-custom" class="tab-button px-3 py-1.5 rounded-lg text-xs font-semibold tracking-wide transition-all text-slate-500 hover:text-slate-700">
                    <i class="hgi hgi-stroke hgi-paint-board mr-1.5"></i>
                    Custom Templates
                </button>
            </div>
        </div>

        <!-- Modal Body / Content Grid -->
        <div class="overflow-y-auto flex-1 p-4">
            <!-- Predefined Templates Grid -->
            <div id="predefined-templates" class="template-tab-content">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                    @if(isset($defaultList) && count($defaultList) > 0)
                        @foreach($defaultList as $template)
                        @php $template_id = $template->template_id; @endphp
                        <div class="template-card group relative cursor-pointer border rounded-xl overflow-hidden hover:border-slate-300 hover:shadow-sm transition-all duration-200 w-full {{ $campaign->template_id == $template->id ? 'border-[#007682]' : 'border-slate-200' }}" 
                             data-template-db-id="{{ $template->id }}"
                             data-template-unique-id="{{ $template_id }}"
                             onclick="selectTemplate({{ $template->id }}, '{{ $template_id }}')">
                            <div class="relative h-32 overflow-hidden flex items-center justify-center bg-slate-50 rounded-t-lg">
                                @if(file_exists(public_path("templates/user/" . $template_id . "/thumb.png")))
                                    <div class="absolute inset-0 bg-cover bg-top transition-all duration-300 group-hover:scale-105"
                                        style="background-image: url('{{ asset('templates/user/'. $template_id. '/thumb.png') }}')">
                                    </div>
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200/50 flex flex-col p-3 justify-between select-none">
                                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-1">
                                            <div class="h-2 w-8 bg-slate-300/80 rounded"></div>
                                            <div class="flex gap-1">
                                                <div class="h-1 w-1 rounded-full bg-slate-300"></div>
                                            </div>
                                        </div>
                                        <div class="flex-1 flex flex-col justify-center gap-1.5 py-1">
                                            <div class="h-3 w-3/4 bg-slate-300/80 rounded"></div>
                                            <div class="h-1.5 w-full bg-slate-300/40 rounded"></div>
                                            <div class="h-1.5 w-2/3 bg-slate-300/40 rounded"></div>
                                        </div>
                                        <div class="flex items-center gap-2 border-t border-slate-200/50 pt-1">
                                            <div class="h-4 w-12 bg-gradient-to-br from-[#007682]/70 to-[#408b86]/70 rounded"></div>
                                        </div>
                                    </div>
                                @endif
                                @if($campaign->template_id == $template->id)
                                <div class="absolute top-2 right-2 bg-[#007682] text-white px-2 py-0.5 rounded-full text-[10px] font-bold uppercase flex items-center gap-1 z-10 shadow-xs">
                                    <i class="hgi hgi-stroke hgi-tick-02"></i>Active
                                </div>
                                @endif
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="px-3 py-2 bg-white border-t border-slate-100 flex items-center justify-between">
                                <h3 class="font-bold text-slate-800 text-xs truncate">{{ $template->name }}</h3>
                                <span class="text-[10px] text-slate-400 font-medium">Predefined</span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-span-full text-center py-10">
                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <i class="hgi hgi-stroke hgi-folder-open text-xl"></i>
                            </div>
                            <p class="text-slate-500 font-bold text-xs">No predefined templates available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Custom Templates Grid -->
            <div id="custom-templates" class="template-tab-content hidden">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                    @if(isset($mylist) && count($mylist) > 0)
                        @foreach($mylist as $template)
                        @php $template_id = $template->template_id; @endphp
                        <div class="template-card group relative cursor-pointer border rounded-xl overflow-hidden hover:border-slate-300 hover:shadow-sm transition-all duration-200 w-full {{ $campaign->template_id == $template->id ? 'border-[#007682]' : 'border-slate-200' }}" 
                             data-template-db-id="{{ $template->id }}"
                             data-template-unique-id="{{ $template_id }}"
                             onclick="selectTemplate({{ $template->id }}, '{{ $template_id }}')">
                            <div class="relative h-32 overflow-hidden flex items-center justify-center bg-slate-50 rounded-t-lg">
                                @if(file_exists(public_path("templates/user/" . $template_id . "/thumb.png")))
                                    <div class="absolute inset-0 bg-cover bg-top transition-all duration-300 group-hover:scale-105"
                                        style="background-image: url('{{ asset('templates/user/'. $template_id. '/thumb.png') }}')">
                                    </div>
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200/50 flex flex-col p-3 justify-between select-none">
                                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-1">
                                            <div class="h-2 w-8 bg-slate-300/80 rounded"></div>
                                            <div class="flex gap-1">
                                                <div class="h-1 w-1 rounded-full bg-slate-300"></div>
                                            </div>
                                        </div>
                                        <div class="flex-1 flex flex-col justify-center gap-1.5 py-1">
                                            <div class="h-3 w-3/4 bg-slate-300/80 rounded"></div>
                                            <div class="h-1.5 w-full bg-slate-300/40 rounded"></div>
                                            <div class="h-1.5 w-2/3 bg-slate-300/40 rounded"></div>
                                        </div>
                                        <div class="flex items-center gap-2 border-t border-slate-200/50 pt-1">
                                            <div class="h-4 w-12 bg-gradient-to-br from-[#007682]/70 to-[#408b86]/70 rounded"></div>
                                        </div>
                                    </div>
                                @endif
                                @if($campaign->template_id == $template->id)
                                <div class="absolute top-2 right-2 bg-[#007682] text-white px-2 py-0.5 rounded-full text-[10px] font-bold uppercase flex items-center gap-1 z-10 shadow-xs">
                                    <i class="hgi hgi-stroke hgi-tick-02"></i>Active
                                </div>
                                @endif
                                <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="px-3 py-2 bg-white border-t border-slate-100 flex items-center justify-between">
                                <h3 class="font-bold text-slate-800 text-xs truncate">{{ $template->name }}</h3>
                                <span class="text-[10px] text-slate-400 font-medium">Custom</span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-span-full text-center py-10">
                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <i class="hgi hgi-stroke hgi-folder-open text-xl"></i>
                            </div>
                            <p class="text-slate-500 font-bold text-xs">No custom templates created yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-slate-50 px-5 py-3 border-t border-slate-200/80 flex items-center justify-end gap-2.5 flex-shrink-0">
            <button type="button" onclick="closeTemplateModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-100 transition-colors">
                Cancel
            </button>
            <button type="button" onclick="applyTemplate()" class="px-5 py-2 rounded-xl bg-gradient-to-br from-[#007682] to-[#408b86] text-white font-bold text-xs tracking-wide hover:brightness-105 transition-all shadow-md flex items-center gap-1.5">
                <i class="hgi hgi-stroke hgi-tick-02 text-sm"></i>
                Apply Template
            </button>
        </div>
    </div>
</div>


<style>
@keyframes modal-in {
    from {
        opacity: 0;
        transform: scale(0.97);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-modal {
    animation: modal-in 0.18s cubic-bezier(0.16, 1, 0.3, 1);
}

.tab-button.active {
    background-color: #ffffff;
    color: #007682;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.tab-button:not(.active) {
    color: #64748b;
    background-color: transparent;
}

.tab-button:not(.active):hover {
    color: #1e293b;
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
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 border border-slate-200/80 rounded-lg text-xs font-medium text-slate-700">
                    ${item.label}
                    <button type="button" onclick="removeGroup('${item.id}')" class="text-slate-400 hover:text-red-500 transition-colors">
                        <i class="hgi hgi-stroke hgi-cancel-01 text-xs"></i>
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
            btn.classList.remove('active', 'bg-white', 'text-[#007682]', 'shadow-xs');
            btn.classList.add('text-slate-500', 'bg-transparent');
        });
        
        const activeTab = document.getElementById('tab-' + tab);
        if (activeTab) {
            activeTab.classList.add('active', 'bg-white', 'text-[#007682]', 'shadow-xs');
            activeTab.classList.remove('text-slate-500', 'bg-transparent');
        }
        
        document.querySelectorAll('.template-tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        const targetContent = document.getElementById(tab + '-templates');
        if (targetContent) {
            targetContent.classList.remove('hidden');
        }
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
            card.classList.remove('border-[#007682]', 'ring-2', 'ring-[#007682]/20', 'border-teal-500', 'ring-4', 'ring-teal-500/20');
            card.classList.add('border-slate-200');
            
            // Remove active badge if exists
            const activeBadge = card.querySelector('.active-badge');
            if (activeBadge) {
                activeBadge.remove();
            }
        });
        
        // Add selection to clicked card using DB ID
        const selectedCard = document.querySelector(`.template-card[data-template-db-id="${dbID}"]`);
        if (selectedCard) {
            selectedCard.classList.remove('border-slate-200');
            selectedCard.classList.add('border-[#007682]');
            
            // Add active badge
            const imageContainer = selectedCard.querySelector('.relative.h-32') || selectedCard.querySelector('.relative');
            if (imageContainer && !imageContainer.querySelector('.active-badge')) {
                const badge = document.createElement('div');
                badge.className = 'active-badge absolute top-2 right-2 bg-[#007682] text-white px-2 py-0.5 rounded-full text-[10px] font-bold uppercase flex items-center gap-1 z-10 shadow-xs';
                badge.innerHTML = '<i class="hgi hgi-stroke hgi-tick-02"></i>Active';
                imageContainer.appendChild(badge);
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

        // Get the template name from the card
        const templateName = selectedCard.querySelector('h3')?.textContent || 'Template';

        // Try to extract background image from the card
        const previewDiv = selectedCard.querySelector('.relative.h-32') || selectedCard.querySelector('.relative');
        let bgImage = '';
        if (previewDiv) {
            const bgDiv = previewDiv.querySelector('[style*="background-image"]');
            if (bgDiv) {
                const match = bgDiv.style.backgroundImage.match(/url\(["']?([^"']+)["']?\)/);
                if (match) bgImage = match[1];
            }
        }

        // Update the preview div permanently
        const previewContainer = document.getElementById('template-preview');
        if (previewContainer) {
            if (bgImage) {
                previewContainer.innerHTML = `
                    <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="${bgImage}" alt="Template Preview" />
                    <div class="absolute inset-0 bg-teal-900/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                `;
            } else {
                const firstLetter = templateName.charAt(0).toUpperCase();
                previewContainer.innerHTML = `
                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200/50 flex items-center justify-center">
                        <div class="text-center">
                            <span class="text-7xl font-black text-white/20 select-none block">${firstLetter}</span>
                            <span class="text-slate-400 text-sm font-bold mt-2 block">${templateName}</span>
                        </div>
                    </div>
                `;
            }
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
