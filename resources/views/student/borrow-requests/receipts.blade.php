<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">My Receipts</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Borrow Receipts</h3>

                @if($borrowRequests->count())
                    <div class="space-y-4">
                        @foreach($borrowRequests as $br)
                                    <div class="p-4 border rounded-md flex items-center justify-between border-slate-700">
                                <div>
                                            <div class="font-semibold text-gray-100">{{ $br->book->title }}</div>
                                            <div class="text-sm text-gray-400">Borrowed: {{ optional($br->borrowed_at)->format('M j, Y g:i A') ?? 'N/A' }} • Due: {{ optional($br->due_at)->format('M j, Y') ?? 'N/A' }}</div>
                                </div>
                                <div class="flex items-center gap-3">
                                            <a href="{{ route('student.borrow-requests.receipt', $br->id) }}" class="px-3 py-2 bg-gray-700 text-gray-100 rounded text-sm">View Receipt</a>
                                            <span class="text-sm text-gray-400">Status: {{ ucfirst($br->status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $borrowRequests->links() }}
                    </div>
                @else
                    <div class="p-6 text-center text-gray-600">No receipts found.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>