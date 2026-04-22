<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-100">Confirm Borrow Request</h1>
    </x-slot>

    <div class="rounded-xl p-6 shadow-card bg-slate-800 border border-slate-700 text-gray-100">
        <h2 class="font-semibold mb-4 text-gray-100">Request Details</h2>
        <p><strong>Member:</strong> {{ $borrowRequest->student->name ?? 'N/A' }}</p>
        <p><strong>Book:</strong> {{ $borrowRequest->book->title ?? 'N/A' }}</p>
        <p><strong>Requested On:</strong> {{ $borrowRequest->created_at->format('M j, Y g:i A') }}</p>

        <div class="mt-6 flex gap-3">
            <form method="POST" action="{{ route('staff.borrow-requests.confirm', $borrowRequest->id) }}">
                @csrf
                <button type="submit" data-confirm="Confirm approving this borrow request?" class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-600">Confirm</button>
            </form>

            <a href="{{ route('staff.dashboard.index') }}" class="px-4 py-2 bg-slate-700 text-gray-100 rounded hover:bg-slate-600">Cancel</a>
        </div>
    </div>
</x-app-layout>