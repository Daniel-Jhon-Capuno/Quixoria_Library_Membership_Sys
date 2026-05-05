<div id="reject-reason-{{ $borrow->id }}" class="hidden mt-4 p-6 bg-gradient-to-r from-slate-900/95 to-slate-900/50 backdrop-blur-xl border border-orange-500/50 rounded-2xl shadow-2xl animate-in fade-in duration-200">
    <button class="mb-6 p-4 bg-orange-500/10 hover:bg-orange-500/20 border-2 border-orange-500/30 rounded-2xl flex items-start gap-4 hover:border-orange-400/50 transition-all group cursor-pointer">
        <div class="flex-shrink-0 w-12 h-12 bg-orange-500/20 rounded-xl flex items-center justify-center border-2 border-orange-500/40 group-hover:border-orange-400/60 shadow-lg">
            <svg class="w-6 h-6 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <h4 class="text-lg font-bold text-orange-100 mb-1">Return Rejected</h4>
            <p class="text-sm font-medium text-orange-200 group-hover:text-orange-100">Click to see reason & next steps</p>
        </div>
    </button>
    <div class="mb-6">
        <h5 class="font-semibold text-slate-300 mb-3 text-base">📋 Staff Reason:</h5>
        <div class="bg-slate-900/70 p-4 rounded-xl border-l-4 border-orange-400 text-slate-200 leading-relaxed whitespace-pre-wrap text-sm backdrop-blur-sm">{{ $borrow->rejection_reason }}</div>
    </div>
    <div class="bg-gradient-to-r from-orange-500/10 to-red-500/10 border border-orange-400/50 p-4 rounded-xl text-sm">
        <p class="text-orange-200 font-medium mb-2">✅ Next Steps:</p>
        <ul class="text-orange-100 space-y-1 ml-4 list-disc">
            <li>Review book condition and reason above</li>
            <li>Contact library staff to resolve issue</li>
            <li>Return book after resolution</li>
            <li>Book will be checked in once resolved</li>
        </ul>
    </div>
    <div class="mt-6 pt-6 border-t border-orange-500/30 flex gap-3">
        <button onclick="document.getElementById('reject-reason-{{ $borrow->id }}').classList.add('hidden')" class="flex-1 px-6 py-3 bg-slate-800/80 hover:bg-slate-700 text-slate-200 font-semibold rounded-xl border border-slate-600 hover:border-slate-500 transition-all text-sm">
            Close
        </button>
        <a href="{{ route('student.book-catalog.show', $borrow->book) }}" class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-500 hover:to-orange-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all text-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            Book Details
        </a>
    </div>
</div>

<script>
function showRejectReason(id) {
    const reasonDiv = document.getElementById('reject-reason-' + id);
    if (reasonDiv) {
        reasonDiv.classList.remove('hidden');
    }
}
</script>
