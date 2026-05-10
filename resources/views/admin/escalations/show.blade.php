@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('admin.escalations.index') }}" class="text-cyan-400 hover:text-cyan-300 font-medium">← Back to Dashboard</a>
            <h1 class="text-3xl font-bold text-white mt-4">Case #{{ $borrow->id }}</h1>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-8">
            <!-- Case Info -->
            <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8">
                <h3 class="text-xl font-bold text-white mb-6">Case Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Student</label>
                        <p class="text-white font-medium">{{ $borrow->student->name }}</p>
                        <p class="text-slate-400 text-sm">{{ $borrow->student->email }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Book Title</label>
                        <p class="text-white font-medium">{{ $borrow->book->title }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Status</label>
                        <p class="text-white font-medium capitalize">{{ str_replace('_', ' ', $borrow->status) }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Escalation Level</label>
                        <p class="text-white font-medium capitalize">{{ $borrow->escalation_level ?? 'None' }}</p>
                    </div>
                </div>
            </div>

            <!-- Fee Breakdown -->
            <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8">
                <h3 class="text-xl font-bold text-white mb-6">Fee Breakdown</h3>
                <div class="space-y-3 mb-6 pb-6 border-b border-slate-700/50">
                    <div class="flex justify-between">
                        <span class="text-slate-300">Late Fees:</span>
                        <span class="text-white font-bold">₱{{ (($borrow->late_fee_charged ?? 0) / 100) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-300">Replacement Fee:</span>
                        <span class="text-white font-bold">₱{{ (($borrow->replacement_fee_cents ?? 0) / 100) }}</span>
                    </div>
                    <div class="flex justify-between text-lg">
                        <span class="text-cyan-400 font-bold">Total Due:</span>
                        <span class="text-cyan-300 font-bold">₱{{ (((($borrow->late_fee_charged ?? 0) + ($borrow->replacement_fee_cents ?? 0)) / 100)) }}</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Days Borrowed:</span>
                        <span class="text-slate-300">{{ $borrow->borrowed_at->diffInDays($borrow->due_at) }} days</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Days Overdue:</span>
                        <span class="text-slate-300">{{ $borrow->due_at->diffInDays(now()) }} days</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8 mb-8">
            <h3 class="text-xl font-bold text-white mb-6">Timeline</h3>
            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="text-sm font-semibold text-slate-400 w-24">{{ $borrow->borrowed_at->format('M d, Y') }}</div>
                    <div class="text-slate-300">Borrowed</div>
                </div>
                <div class="flex gap-4">
                    <div class="text-sm font-semibold text-slate-400 w-24">{{ $borrow->due_at->format('M d, Y') }}</div>
                    <div class="text-slate-300">Due date</div>
                </div>
                @foreach($borrow->escalationLogs as $log)
                    <div class="flex gap-4">
                        <div class="text-sm font-semibold text-slate-400 w-24">{{ $log->created_at->format('M d, Y') }}</div>
                        <div class="text-slate-300 capitalize">{{ str_replace('_', ' ', $log->level) }}: {{ $log->note }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Actions -->
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8">
            <h3 class="text-xl font-bold text-white mb-6">Admin Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <button onclick="openResolveModal()" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all">
                    ✅ Mark Resolved & Unblock
                </button>
                <button onclick="openTemporaryUnblockModal()" class="px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium transition-all">
                    ⚠️ Temporarily Unblock
                </button>
                <button onclick="openResetEscalationModal()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all">
                    🔄 Reset Escalation
                </button>
                <button onclick="openBanModal()" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all">
                    ❌ Permanently Ban
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Resolve Modal -->
<div id="resolveModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-8 max-w-md w-full shadow-2xl">
        <h3 class="text-2xl font-bold text-white mb-6">Mark Resolved</h3>
        <form onsubmit="handleResolve(event)">
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Amount Received (₱)</label>
                    <input type="number" step="0.01" id="resolveAmount" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Payment Method</label>
                    <select id="resolveMethod" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white" required>
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Notes (optional)</label>
                    <textarea id="resolveNotes" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white" rows="3"></textarea>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('resolveModal')" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">Resolve</button>
            </div>
        </form>
    </div>
</div>

<!-- Temporary Unblock Modal -->
<div id="tempUnblockModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-8 max-w-md w-full shadow-2xl">
        <h3 class="text-2xl font-bold text-white mb-6">Temporarily Unblock Account</h3>
        <form onsubmit="handleTemporaryUnblock(event)">
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Days of Access</label>
                    <input type="number" min="1" max="30" id="tempDays" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white" value="7" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Reason</label>
                    <textarea id="tempReason" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white" rows="3" required></textarea>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('tempUnblockModal')" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium">Unblock</button>
            </div>
        </form>
    </div>
</div>

<!-- Reset Escalation Modal -->
<div id="resetModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-8 max-w-md w-full shadow-2xl">
        <h3 class="text-2xl font-bold text-white mb-6">Reset Escalation</h3>
        <form onsubmit="handleReset(event)">
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">New Due Date</label>
                    <input type="date" id="resetDueDate" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Reason</label>
                    <textarea id="resetReason" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white" rows="3" required></textarea>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('resetModal')" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">Reset</button>
            </div>
        </form>
    </div>
</div>

<!-- Ban Modal -->
<div id="banModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-8 max-w-md w-full shadow-2xl">
        <h3 class="text-2xl font-bold text-red-400 mb-2">⚠️ Permanent Ban</h3>
        <p class="text-slate-300 text-sm mb-6">This action cannot be undone. The student will not be able to borrow anymore.</p>
        <form onsubmit="handleBan(event)">
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Ban Reason</label>
                    <textarea id="banReason" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white" rows="4" required></textarea>
                </div>
                <div class="bg-red-900/30 border border-red-600/50 p-3 rounded-lg">
                    <p class="text-red-300 text-sm font-medium">Confirm: Student {{ $borrow->student->name }} will be permanently banned</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('banModal')" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">Ban Permanently</button>
            </div>
        </form>
    </div>
</div>

<script>
function openResolveModal() { document.getElementById('resolveModal').classList.remove('hidden'); }
function openTemporaryUnblockModal() { document.getElementById('tempUnblockModal').classList.remove('hidden'); }
function openResetEscalationModal() { document.getElementById('resetModal').classList.remove('hidden'); }
function openBanModal() { document.getElementById('banModal').classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') {
        document.querySelectorAll('[id$="Modal"]').forEach(m => m.classList.add('hidden'));
    }
});

function handleResolve(event) {
    event.preventDefault();
    const amount = parseFloat(document.getElementById('resolveAmount').value) * 100;
    const method = document.getElementById('resolveMethod').value;
    const notes = document.getElementById('resolveNotes').value;

    fetch("{{ route('admin.escalations.resolve', $borrow->id) }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ amount_cents: Math.round(amount), method, notes })
    }).then(r => r.json()).then(d => {
        alert('Case resolved! Student account has been cleared.');
        window.location.reload();
    }).catch(e => alert('Error: ' + e));
}

function handleTemporaryUnblock(event) {
    event.preventDefault();
    const days = parseInt(document.getElementById('tempDays').value);
    const reason = document.getElementById('tempReason').value;

    fetch("{{ route('admin.escalations.temporary-unblock', $borrow->id) }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ days, reason })
    }).then(r => r.json()).then(d => {
        alert(`Student temporarily unblocked for ${days} day(s)`);
        window.location.reload();
    }).catch(e => alert('Error: ' + e));
}

function handleReset(event) {
    event.preventDefault();
    const dueDate = document.getElementById('resetDueDate').value;
    const reason = document.getElementById('resetReason').value;

    fetch("{{ route('admin.escalations.reset', $borrow->id) }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ due_at: dueDate, reason })
    }).then(r => r.json()).then(d => {
        alert('Escalation reset! New due date set.');
        window.location.reload();
    }).catch(e => alert('Error: ' + e));
}

function handleBan(event) {
    event.preventDefault();
    const reason = document.getElementById('banReason').value;
    const confirmed = confirm('Are you absolutely sure? This will permanently ban the student.');
    
    if(!confirmed) return;

    fetch("{{ route('admin.escalations.ban', $borrow->student->id) }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ reason })
    }).then(r => r.json()).then(d => {
        alert('Student permanently banned');
        window.location.href = "{{ route('admin.escalations.index') }}";
    }).catch(e => alert('Error: ' + e));
}
</script>
@endsection
