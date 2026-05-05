<div id="rejectReturnModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 w-[600px] max-w-4xl shadow-2xl rounded-2xl">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6">
            <h3 class="text-2xl font-bold text-white mb-6">Reject Return Request</h3>
            <form id="rejectReturnForm" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="reject_return_reason" class="block text-lg font-semibold text-slate-200 mb-3">Reason for Rejection</label>
                    <textarea name="rejection_reason" id="reject_return_reason" rows="8" class="w-full p-6 bg-slate-900/80 border-2 border-slate-600 rounded-2xl shadow-xl focus:border-teal-glow focus:ring-4 ring-teal-glow/30 focus:outline-none transition-all placeholder-slate-500 text-slate-100 text-lg resize-vertical min-h-[250px]" placeholder="Enter detailed reason why this return request is being rejected...&#10;&#10;Examples:&#10;- Book condition: torn pages, water damage, missing cover&#10;- Unpaid late fees from previous borrows&#10;- Returned to wrong location (main desk vs dropbox)&#10;- Book scanned but not physically received&#10;&#10;Student will receive this exact reason via notification." required></textarea>
                </div>
                <p class="text-slate-400 mb-8 text-base leading-relaxed">This detailed reason will be sent to the student via notification with appeal instructions.</p>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="showFinalConfirm()" class="px-8 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-base w-full md:w-auto">✓ Confirm & Send Rejection</button>
                </div>
                <div id="finalConfirm" class="mt-6 p-6 bg-amber-900/50 border-2 border-amber-500 rounded-2xl hidden shadow-2xl">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-amber-500/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l1.293 1.293a1 1 0 001.414 0l3.293-3.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-amber-100">Final Confirmation</h4>
                    </div>
                    <p class="text-amber-200 mb-6 text-lg leading-relaxed">This rejection + reason will be sent immediately via notification.</p>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectReturnModal()" class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold rounded-xl transition-all duration-200 text-base shadow-md hover:shadow-lg">Cancel</button>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-base">🚫 Send Rejection</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectReturnModal(requestId) {
    document.getElementById('rejectReturnForm').action = `/staff/borrow-requests/${requestId}/reject-return`;
    document.getElementById('rejectReturnModal').classList.remove('hidden');
    document.getElementById('finalConfirm').classList.add('hidden');
    document.getElementById('reject_return_reason').value = '';
    document.body.style.overflow = 'hidden';
}

function showFinalConfirm() {
    document.getElementById('finalConfirm').classList.remove('hidden');
}

function closeRejectReturnModal() {
    document.getElementById('rejectReturnModal').classList.add('hidden');
    document.getElementById('finalConfirm').classList.add('hidden');
    document.body.style.overflow = '';
}

document.querySelectorAll('button[onclick*="openRejectReturnModal"]').forEach(btn => {
    btn.addEventListener('click', function() {
        openRejectReturnModal(this.dataset.requestId || this.getAttribute('onclick').match(/\((\d+)\)/)?.[1]);
    });
});
</script>
