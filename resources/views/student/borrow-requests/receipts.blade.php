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
                            <div class="group p-6 bg-gradient-to-r from-slate-800 to-slate-900/50 border border-slate-700/50 rounded-2xl hover:border-cyan-400/50 hover:shadow-xl hover:shadow-cyan-500/20 hover:scale-[1.02] transition-all duration-300 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center shadow-lg border border-cyan-400/30 p-2 flex-shrink-0">
                                        <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-lg text-gray-100 group-hover:text-cyan-300 transition-colors line-clamp-1">{{ $br->book->title }}</div>
                                        <div class="text-sm text-slate-400">Borrowed: {{ optional($br->borrowed_at)->format('M j, Y g:i A') ?? 'N/A' }} • Due: {{ optional($br->due_at)->format('M j, Y') ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <a href="{{ route('student.borrow-requests.receipt', $br->id) }}" class="group-hover:bg-gradient-to-r group-hover:from-cyan-500 group-hover:to-blue-600 px-4 py-2 bg-slate-700 text-gray-100 rounded-lg text-sm font-medium shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200 border border-slate-600 hover:border-cyan-400/50">View Receipt</a>
                                    <span class="px-3 py-1 bg-slate-800/50 text-slate-300 text-xs font-semibold rounded-full border border-slate-700">{{ ucfirst($br->status) }}</span>
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