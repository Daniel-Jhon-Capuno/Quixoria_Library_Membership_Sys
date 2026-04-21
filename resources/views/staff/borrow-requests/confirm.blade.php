<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold">Confirm Borrow Request</h1>
    </x-slot>

    <div class="rounded-xl p-6 shadow-card" style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
        <h2 class="font-semibold mb-4">Request Details</h2>
        <p><strong>Member:</strong> {{ $borrowRequest->student->name ?? 'N/A' }}</p>
        <p><strong>Book:</strong> {{ $borrowRequest->book->title ?? 'N/A' }}</p>
        <p><strong>Requested On:</strong> {{ $borrowRequest->created_at->format('M j, Y g:i A') }}</p>

        <div class="mt-6 flex gap-3">
            <form method="POST" action="{{ route('staff.borrow-requests.confirm', $borrowRequest->id) }}">
                @csrf
                <button type="submit" data-confirm="Confirm approving this borrow request?" class="px-4 py-2 bg-primary text-white rounded">Confirm</button>
            </form>

            <a href="{{ route('staff.dashboard.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
        </div>
    </div>
</x-app-layout>