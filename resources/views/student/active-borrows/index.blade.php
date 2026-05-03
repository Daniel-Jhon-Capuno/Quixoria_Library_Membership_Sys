<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">My Active Borrows</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Sort Options -->
            <div class="bg-slate-800/50 backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-2xl mb-8 border border-slate-700/50">
                <div class="p-6">
                    <form method="GET" action="{{ route('student.active-borrows.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label for="sort" class="block text-sm font-medium text-slate-300 mb-2">Sort by</label>
                            <select name="sort" id="sort" class="w-full bg-slate-900/80 border border-slate-700 text-slate-100 rounded-xl shadow-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all px-4 py-3">
                                <option value="soonest" {{ request('sort', 'soonest') == 'soonest' ? 'selected' : '' }}>Soonest Due</option>
                                <option value="furthest" {{ request('sort') == 'furthest' ? 'selected' : '' }}>Furthest Due</option>
                                <option value="overdue" {{ request('sort') == 'overdue' ? 'selected' : '' }}>Overdue First</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-cyan-600 to-cyan-700 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wide hover:from-cyan-700 hover:to-cyan-800 shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 000 2h1.25l.707 3.293A1 1 0 006.07 9h11.86a1 1 0 00.977-.857l1.256-5.986A1 1 0 0020 2H4a1 1 0 000 2zM4 10a1 1 0 01-1-1V7a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4zM9 13a1 1 0 01-1-1V12a1 1 0 011-1h6a1 1 0 011 1v1a1 1 0 01-1 1H9z"></path>
                                </svg>
                                Apply Sort
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Borrows Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($activeBorrows as $borrow)
                <div class="group bg-gradient-to-b from-slate-800 via-slate-800/90 to-slate-900/80 backdrop-blur-xl rounded-2xl p-8 border border-slate-700 hover:border-cyan-500/50 hover:shadow-2xl hover:shadow-cyan-500/25 hover:-translate-y-1 transition-all duration-300 h-full flex flex-col shadow-xl">
                    <!-- Book Cover -->
                    <div class="aspect-[3/4] bg-slate-900/50 rounded-xl mb-6 overflow-hidden group-hover:scale-[1.02] transition-transform duration-300 shadow-lg">
                        @if($borrow->book->cover_image)
                            <img src="{{ asset('storage/' . $borrow->book->cover_image) }}"
                                 alt="{{ $borrow->book->title }}"
                                 class="w-full h-full object-cover group-hover:brightness-110 transition-all duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center">
                                <svg class="w-16 h-16 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Book Info -->
                    <div class="flex flex-col flex-1 mb-6">
                        <h3 class="text-xl font-bold text-white mb-2 leading-tight line-clamp-2 group-hover:text-cyan-300 transition-colors">{{ $borrow->book->title }}</h3>
                        <p class="text-slate-400 font-medium mb-4">{{ $borrow->book->author }}</p>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-8">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Due Date</div>
                        <div class="text-2xl font-black text-white mb-2">{{ $borrow->due_at->format('M j, Y') }}</div>
                        <div class="flex items-center">
                            @if($borrow->due_at->isPast())
                                <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path>
                                    </svg>
                                    OVERDUE
                                </div>
                            @else
                                <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"></path>
                                    </svg>
                                    {{ $borrow->due_at->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Renewal Info -->
                    <div class="mb-8">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Renewals</div>
                        <div class="flex items-center space-x-2">
                            <div class="w-12 h-3 bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500" style="width: {{ min(100, ($borrow->renewals_used / max(1, auth()->user()->subscription?->membershipTier?->renewal_limit ?? 1)) * 100) }}%"></div>
                            </div>
                            <span class="text-lg font-bold text-slate-200">{{ $borrow->renewals_used }} / {{ auth()->user()->subscription?->membershipTier?->renewal_limit ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons - Fixed Layout -->
                    <div class="space-y-3">
                        @php
                            $canRenew = $borrow->renewals_used < (auth()->user()->subscription?->membershipTier?->renewal_limit ?? 0)
                                          && !$borrow->due_at->isPast();
                        @endphp

                        @if($canRenew)
                            <form method="POST" action="{{ route('student.active-borrows.renew', $borrow->id) }}" class="block">
                                @csrf
                                <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-200 text-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-slate-900 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Renew Book
                                </button>
                            </form>
                        @else
                            <div class="w-full px-6 py-4 bg-slate-800/80 border-2 border-dashed border-slate-600 rounded-2xl text-center text-slate-400">
                                <div class="text-sm font-medium mb-1">{{ $borrow->due_at->isPast() ? 'Overdue - Cannot Renew' : 'Max Renewals Reached' }}</div>
                                <div class="text-xs">Return first or wait for due date</div>
                            </div>
                        @endif

                        <!-- Request Return Button -->
                        <form method="POST" action="{{ route('student.active-borrows.return-request', $borrow->id) }}" class="block">
                            @csrf
                            <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-200 text-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-slate-900 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Request Return
                            </button>
                        </form>

                        <a href="{{ route('student.book-catalog.show', $borrow->book) }}" class="block w-full text-center px-6 py-4 bg-gradient-to-r from-slate-700 to-slate-800 hover:from-slate-600 hover:to-slate-700 text-slate-200 font-semibold rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 text-lg border border-slate-600 hover:border-slate-500 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            View Book Details
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <div class="bg-gradient-to-b from-slate-800/50 to-slate-900/50 backdrop-blur-xl rounded-3xl p-16 text-center border border-slate-700/50 shadow-2xl">
                        <svg class="mx-auto h-24 w-24 text-slate-600 mb-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="text-2xl font-bold text-slate-200 mb-4">No Active Borrows</h3>
                        <p class="text-lg text-slate-500 mb-8 max-w-md mx-auto leading-relaxed">You don't have any books currently borrowed. Start browsing our library!</p>
                        <a href="{{ route('student.book-catalog.index') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 text-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Browse Books Now
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Summary Stats -->
            @if($activeBorrows->count() > 0)
            <div class="mt-12 bg-gradient-to-r from-slate-800/60 to-slate-900/60 backdrop-blur-xl rounded-3xl p-8 border border-slate-700/50 shadow-2xl">
                <h3 class="text-2xl font-bold text-white mb-8 text-center">📊 Borrow Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group text-center p-8 rounded-2xl bg-slate-900/50 border border-slate-700 hover:border-cyan-500/50 hover:bg-slate-800/70 transition-all duration-300 hover:shadow-2xl hover:shadow-cyan-500/20">
                        <div class="text-4xl font-black text-cyan-400 mb-4">{{ $activeBorrows->count() }}</div>
                        <div class="text-xl font-bold text-slate-300 mb-1">Total Active</div>
                        <div class="text-sm text-slate-500">Books Borrowed</div>
                    </div>
                    <div class="group text-center p-8 rounded-2xl bg-slate-900/50 border border-slate-700 hover:border-red-500/50 hover:bg-slate-800/70 transition-all duration-300 hover:shadow-2xl hover:shadow-red-500/20">
                        <div class="text-4xl font-black text-red-400 mb-4">{{ $activeBorrows->where('due_at', '<', now())->count() }}</div>
                        <div class="text-xl font-bold text-slate-300 mb-1">Overdue</div>
                        <div class="text-sm text-slate-500">Books (Return ASAP!)</div>
                    </div>
                    <div class="group text-center p-8 rounded-2xl bg-slate-900/50 border border-slate-700 hover:border-emerald-500/50 hover:bg-slate-800/70 transition-all duration-300 hover:shadow-2xl hover:shadow-emerald-500/20">
                        <div class="text-4xl font-black text-emerald-400 mb-4">{{ $activeBorrows->where('due_at', '>=', now())->count() }}</div>
                        <div class="text-xl font-bold text-slate-300 mb-1">On Time</div>
                        <div class="text-sm text-slate-500">Books</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
