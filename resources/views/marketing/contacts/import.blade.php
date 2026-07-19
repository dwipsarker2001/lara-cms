@extends('marketing.layouts.app')

@section('content')
<div class="w-full min-h-screen bg-slate-50/50 px-8 py-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Import Contacts</h1>
            <p class="text-slate-500 mt-1.5">Upload your subscriber list using our supported templates.</p>
        </div>
        
        <a href="{{ route('app.contact.index', $groupId) }}" 
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all shadow-sm">
            <i class="hgi hgi-stroke hgi-arrow-left-01 text-lg"></i>
            Back to List
        </a>
    </div>

    @if(session('error'))
        <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg shadow-sm flex items-center gap-3">
            <i class="hgi hgi-stroke hgi-alert-01 text-xl"></i>
            <span class="text-sm font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                <div class="w-12 h-12 bg-teal-100 text-teal-600 rounded-xl flex items-center justify-center mb-4">
                    <i class="hgi hgi-stroke hgi-mail-open-love text-2xl"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-900">Hybridmail Template</h2>
                <p class="text-sm text-slate-500 mt-1">Standard format for custom contact lists.</p>
            </div>

            <div class="p-8 flex-1">
                <div class="mb-8">
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4">Download Templates</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ asset('public/assets/templates/contact/samplecontact.csv') }}" download 
                           class="flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-all">
                            <i class="hgi hgi-stroke hgi-csv text-lg text-teal-600"></i> CSV Template
                        </a>
                        <a href="{{ asset('public/assets/templates/contact/samplecontact.txt') }}" download 
                           class="flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-all">
                            <i class="hgi hgi-stroke hgi-text-font text-lg text-indigo-600"></i> TXT Template
                        </a>
                    </div>
                </div>

                <form id="import_form_hybrid" method="post" action="{{ route('app.contact.fileimport', $groupId) }}" enctype="multipart/form-data">
                    @csrf
                    <input value="hybrid" name="type" hidden />
                    <input type="file" name="file" id="file-upload-input-hybrid" accept=".csv, .txt" class="hidden">
                    
                    <div id="file-upload-select-hybrid" class="group cursor-pointer border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center hover:border-teal-400 hover:bg-teal-50/30 transition-all">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <i class="hgi hgi-stroke hgi-cloud-upload text-2xl text-slate-400 group-hover:text-teal-600"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-700" id="file-select-name-hybrid">Click to upload or drag and drop</p>
                        <p class="text-xs text-slate-400 mt-1">CSV or TXT files only</p>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-8 border-b border-slate-100 bg-slate-50/50">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                    <i class="hgi hgi-stroke hgi-google text-2xl"></i>
                </div>
                <h2 class="text-xl font-bold text-slate-900">Google Contacts</h2>
                <p class="text-sm text-slate-500 mt-1">Directly import your Google export files.</p>
            </div>

            <div class="p-8 flex-1">
                <div class="mb-8">
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4">Download Templates</p>
                    <a href="{{ asset('public/assets/templates/contact/samplegoogle.csv') }}" download 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition-all">
                        <i class="hgi hgi-stroke hgi-csv text-lg text-blue-600"></i> Google CSV Template
                    </a>
                </div>

                <form id="import_form_google" method="post" action="{{ route('app.contact.fileimport', $groupId) }}" enctype="multipart/form-data">
                    @csrf
                    <input value="google" name="type" hidden />
                    <input type="file" name="file" id="file-upload-input-google" accept=".csv" class="hidden">
                    
                    <div id="file-upload-select-google" class="group cursor-pointer border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center hover:border-blue-400 hover:bg-blue-50/30 transition-all">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <i class="hgi hgi-stroke hgi-cloud-upload text-2xl text-slate-400 group-hover:text-blue-600"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-700" id="file-select-name-google">Select Google CSV file</p>
                        <p class="text-xs text-slate-400 mt-1">Google export format supported</p>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    // Hybrid Upload Logic
    const hybridSelect = document.getElementById("file-upload-select-hybrid");
    const hybridInput = document.getElementById("file-upload-input-hybrid");
    const hybridName = document.getElementById("file-select-name-hybrid");

    hybridSelect.onclick = () => hybridInput.click();
    hybridInput.onchange = () => {
        if(hybridInput.files.length > 0) {
            hybridName.innerText = hybridInput.files[0].name;
            hybridName.classList.add('text-teal-600');
            // Adding a small delay to let user see the filename before submit
            setTimeout(() => document.getElementById("import_form_hybrid").submit(), 500);
        }
    };

    // Google Upload Logic
    const googleSelect = document.getElementById("file-upload-select-google");
    const googleInput = document.getElementById("file-upload-input-google");
    const googleName = document.getElementById("file-select-name-google");

    googleSelect.onclick = () => googleInput.click();
    googleInput.onchange = () => {
        if(googleInput.files.length > 0) {
            googleName.innerText = googleInput.files[0].name;
            googleName.classList.add('text-blue-600');
            setTimeout(() => document.getElementById("import_form_google").submit(), 500);
        }
    };
</script>
@endsection
