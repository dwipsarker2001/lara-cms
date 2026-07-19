@extends('marketing.layouts.app')

<title>ACCOUNT : CONTACT DETAILS</title>

@section('content')
<div class="w-full min-h-full bg-slate-50/50 px-8 py-8">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Contact Details</h1>
            <p class="text-slate-500 font-medium">Details for {{ $contact->firstname }} {{ $contact->lastname }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('app.contact.index', $groupId) }}"
               class="px-5 py-2 rounded-lg bg-white border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 transition">
               Back to Contacts
            </a>

            <!-- Delete Button -->
            <button type="button"
                onclick="openDeleteModal()"
                class="px-5 py-2 rounded-lg bg-red-600 text-white font-bold hover:bg-red-700 transition">
                Delete Contact
            </button>
        </div>
    </div>

    <!-- Contact Info Card -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <h3 class="text-slate-500 font-semibold text-xs uppercase mb-2">Full Name</h3>
                <p class="text-slate-800 font-semibold">{{ $contact->firstname }} {{ $contact->lastname }}</p>
            </div>

            <div>
                <h3 class="text-slate-500 font-semibold text-xs uppercase mb-2">Email</h3>
                <p class="text-slate-800 font-semibold">{{ $contact->email }}</p>
            </div>

            <div>
                <h3 class="text-slate-500 font-semibold text-xs uppercase mb-2">SMS</h3>
                <p class="text-slate-800 font-semibold">{{ $contact->sms ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-slate-500 font-semibold text-xs uppercase mb-2">WhatsApp</h3>
                <p class="text-slate-800 font-semibold">{{ $contact->whatsapp ?? 'N/A' }}</p>
            </div>

            <div>
                <h3 class="text-slate-500 font-semibold text-xs uppercase mb-2">Opt-In Status</h3>
                <span class="px-2 py-1 text-xs font-bold rounded-lg {{ $contact->opt_in == 'Yes' ? 'bg-teal-600 text-white' : 'bg-slate-300 text-slate-700' }}">
                    {{ $contact->opt_in }}
                </span>
            </div>

            <div>
                <h3 class="text-slate-500 font-bold text-xs uppercase mb-2">Subscribed</h3>
                <span class="px-2 py-1 text-xs font-bold rounded-lg {{ $contact->is_unsubscribed ? 'bg-red-800 text-white' : 'bg-teal-600 text-white' }}">
                    {{ $contact->is_unsubscribed ? 'Unsubscribed' : 'Subscribed' }}
                </span>
            </div>

            <div>
                <h3 class="text-slate-500 font-bold text-xs uppercase mb-2">Created At</h3>
                <p class="text-slate-800 font-semibold">{{ $contact->created_at->format('d M, Y h:i A') }}</p>
            </div>

            <div>
                <h3 class="text-slate-500 font-bold text-xs uppercase mb-2">Group ID</h3>
                <p class="text-slate-800 font-semibold">{{ $groupId }}</p>
            </div>

        </div>
    </div>

</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-center">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Delete Contact?</h2>
        <p class="text-sm text-slate-500 mb-6">Are you sure you want to delete this contact? This action cannot be undone.</p>
        <div class="flex gap-3 justify-center">
            <button onclick="closeDeleteModal()"
                class="flex-1 py-2 rounded-lg bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition">
                Cancel
            </button>

            <!-- POST Delete Form -->
            <form id="deleteForm" action="{{ route('app.contact.delete') }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="id" value="{{ $contact->id }}">
                <input type="hidden" name="group_id" value="{{ $groupId }}">
                <button type="submit"
                    class="w-full py-2 rounded-lg bg-red-600 text-white font-bold hover:bg-red-700 transition">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside content
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target.id === 'deleteModal') {
        closeDeleteModal();
    }
});
</script>

@endsection
