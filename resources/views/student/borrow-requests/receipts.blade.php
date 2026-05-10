<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">My Receipts</h2>
    </x-slot>

    <div class="py-6 md:py-12 student-receipts">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-800 overflow-hidden shadow-sm rounded-lg p-4 md:p-6 border border-slate-700">
                <h3 class="text-lg font-medium mb-4 text-gray-100">Borrow Receipts</h3>

                @if($borrowRequests->count())
                    <div class="space-y-4">
                        @foreach($borrowRequests as $br)
                            <div class="group p-4 md:p-6 bg-gradient-to-r from-slate-800 to-slate-900/50 border border-slate-700/50 rounded-2xl hover:border-cyan-400/50 hover:shadow-xl hover:shadow-cyan-500/20 transition-all duration-300 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-500/20 to-blue-500/20 rounded-xl flex items-center justify-center border border-cyan-400/30 flex-shrink-0">
                                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-base md:text-lg text-gray-100 group-hover:text-cyan-300 transition-colors truncate">{{ $br->book->title }}</div>
                                        <div class="text-xs md:text-sm text-slate-400 mt-1">
                                            Borrowed: {{ optional($br->borrowed_at)->format('M j, Y g:i A') ?? 'N/A' }}
                                            <br class="sm:hidden">
                                            <span class="hidden sm:inline"> • </span>
                                            Due: {{ optional($br->due_at)->format('M j, Y') ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0 justify-end">
                                    <a href="{{ route('student.borrow-requests.receipt', $br->id) }}"
                                       class="px-6 py-2 bg-slate-700 hover:bg-gradient-to-r hover:from-cyan-500 hover:to-blue-600 text-gray-100 rounded-lg text-sm font-semibold border border-slate-600 hover:border-cyan-400/50 transition-all duration-200 whitespace-nowrap">
                                        View Receipt
                                    </a>
                                    <span class="px-4 py-2 bg-slate-800/50 text-slate-300 text-xs font-bold rounded-full border border-slate-700 whitespace-nowrap">
                                        {{ ucfirst($br->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $borrowRequests->links() }}
                    </div>
                @else
                    <div class="p-6 text-center text-gray-400">No receipts found.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>