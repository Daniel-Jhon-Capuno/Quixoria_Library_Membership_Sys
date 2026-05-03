
<div id="rejectReturnModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 w-96 max-w-md shadow-2xl rounded-2xl">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6">
            <h3 class="text-xl font-bold text-white mb-4">Reject Return Request</h3>
            <form id="rejectReturnForm" method="POST">
                @csrf
                <input type="hidden" name="rejection_reason" value="Return request rejected by staff">
                <p class="text-slate-300 mb-6 text-sm leading-relaxed">Are you sure you want to reject this return request? The student will keep the book until manual check-in.</p>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectReturnModal()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 font-medium rounded-lg transition-colors text-sm">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 text-sm">Reject Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectReturnModal(requestId) {
    document.getElementById('rejectReturnForm').action = `/staff/borrow-requests/${requestId}/reject-return`;
    document.getElementById('rejectReturnModal').classList.remove('hidden');
}

function closeRejectReturnModal() {
    document.getElementById('rejectReturnModal').classList.add('hidden');
}
</script>

