<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-100">Reject Borrow Request</h1>
    </x-slot>

    <div class="rounded-xl p-6 shadow-card bg-slate-800 border border-slate-700 text-gray-100">
        <h2 class="font-semibold mb-4 text-gray-100">Request Details</h2>
        <p><strong>Member:</strong> {{ $borrowRequest->student->name ?? 'N/A' }}</p>
        <p><strong>Book:</strong> {{ $borrowRequest->book->title ?? 'N/A' }}</p>
        <p><strong>Requested On:</strong> {{ $borrowRequest->created_at->format('M j, Y g:i A') }}</p>

        <form method="POST" action="{{ route('staff.borrow-requests.reject', $borrowRequest->id) }}" class="mt-6">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2 text-gray-300">Rejection Reason</label>
                <textarea name="rejection_reason" rows="4" class="w-full border-slate-700 rounded p-2 bg-slate-900 text-gray-100" required></textarea>
            </div>
            <div class="mt-4 flex gap-3">
                <button type="submit" data-confirm="Are you sure you want to reject this request?" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-500">Reject Request</button>
                <a href="{{ route('staff.dashboard.index') }}" class="px-4 py-2 bg-slate-700 text-gray-100 rounded hover:bg-slate-600">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>