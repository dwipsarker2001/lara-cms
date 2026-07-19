@extends('marketing.layouts.app')

@section('content')
<div class="w-full min-h-screen bg-slate-50/50 px-8 py-8">
    
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Help & Support</h1>
            <p class="text-slate-500 mt-1.5">How can we help you today? Explore our resources or reach out to us.</p>
        </div>
        
        <button onclick="window.history.back()" 
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-all font-bold text-sm shadow-sm">
            <i class="hgi hgi-stroke hgi-arrow-left-01"></i>
            Go Back
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <a href="{{$links['live-chat']}}" target="_blank" 
            class="group bg-white p-8 rounded-2xl border border-slate-200 shadow-sm hover:border-slate-300 hover:shadow-lg hover:shadow-teal-900/5 transition-all duration-300">
            <div class="w-14 h-14 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <i class="hgi hgi-stroke hgi-chat text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Live Chat</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-4">Real-time assistance from our support team for urgent queries.</p>
            <span class="text-teal-600 font-bold text-sm flex items-center gap-2">
                Start Conversation <i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>
            </span>
        </a>

        <a href="{{$links['support-tickets']}}" target="_blank" 
            class="group bg-white p-8 rounded-2xl border border-slate-200 shadow-sm hover:border-slate-300 hover:shadow-lg hover:shadow-teal-900/5 transition-all duration-300">
            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <i class="hgi hgi-stroke hgi-ticket-01 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Support Tickets</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-4">Open a ticket for technical issues and track its progress.</p>
            <span class="text-indigo-600 font-bold text-sm flex items-center gap-2">
                View My Tickets <i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>
            </span>
        </a>

        <a href="{{$links['latest-announcements']}}" target="_blank" 
            class="group bg-white p-8 rounded-2xl border border-slate-200 shadow-sm hover:border-slate-300 hover:shadow-lg hover:shadow-teal-900/5 transition-all duration-300">
            <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <i class="hgi hgi-stroke hgi-megaphone-03 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Latest Announcements</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-4">Stay updated with our newest features and platform updates.</p>
            <span class="text-amber-600 font-bold text-sm flex items-center gap-2">
                Read Updates <i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>
            </span>
        </a>

        <a href="{{$links['faq']}}" target="_blank" 
            class="group bg-white p-8 rounded-2xl border border-slate-200 shadow-sm hover:border-slate-300 hover:shadow-lg hover:shadow-teal-900/5 transition-all duration-300">
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <i class="hgi hgi-stroke hgi-help-circle text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Knowledge Base / FAQ</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-4">Quick answers to common questions about our services.</p>
            <span class="text-blue-600 font-bold text-sm flex items-center gap-2">
                Search Articles <i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>
            </span>
        </a>

        <a href="{{$links['documentation']}}" target="_blank" 
            class="group bg-white p-8 rounded-2xl border border-slate-200 shadow-sm hover:border-slate-300 hover:shadow-lg hover:shadow-teal-900/5 transition-all duration-300">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <i class="hgi hgi-stroke hgi-book-02 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Documentation</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-4">In-depth guides for developers and power users on integration.</p>
            <span class="text-emerald-600 font-bold text-sm flex items-center gap-2">
                Read Docs <i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>
            </span>
        </a>

        <a href="{{$links['report-problem']}}" target="_blank" 
            class="group bg-white p-8 rounded-2xl border border-slate-200 shadow-sm hover:border-slate-300 hover:shadow-lg hover:shadow-red-900/5 transition-all duration-300">
            <div class="w-14 h-14 bg-red-50 text-red-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <i class="hgi hgi-stroke hgi-alert-circle text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Report a Problem</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-4">Found a bug or an error? Let us know so we can fix it.</p>
            <span class="text-red-600 font-bold text-sm flex items-center gap-2">
                Send Report <i class="hgi hgi-stroke hgi-arrow-right-01 text-xs"></i>
            </span>
        </a>

    </div>

    <div class="mt-16 bg-gradient-to-br from-[#007682] to-[#408b86] rounded-3xl p-10 text-center text-white">
        <h2 class="text-2xl font-black mb-3 text-white">Still need help?</h2>
        <p class="text-teal-50 mb-8 max-w-xl mx-auto">If you couldn't find what you were looking for, our dedicated support team is available Mon-Fri, 9am - 6pm.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="mailto:support@hybridmail.com" class="px-8 py-3 bg-white text-teal-800 rounded-xl font-bold hover:bg-teal-50 transition-colors shadow-lg">
                Email Support
            </a>
            <a href="https://www.techics.com/contact" class="px-8 py-3 bg-teal-900/30 text-white border border-white/20 rounded-xl font-bold hover:bg-teal-900/40 transition-colors">
                Call Us Now
            </a>
        </div>
    </div>
</div>
@endsection
