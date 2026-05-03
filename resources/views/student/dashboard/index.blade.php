<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold" style="color: rgb(var(--text-primary));">My Library Dashboard</h1>
                <p class="text-sm mt-1" style="color: rgb(var(--text-secondary));">Manage your books, subscriptions, and reservations</p>
            </div>
        </div>
    </x-slot>

    <!-- Alert Banners -->
    @if(($overdueBorrows ?? collect())->count() > 0)
        <div class="mb-6 rounded-xl p-6 flex items-start gap-4 bg-red-50/20 border border-red-200/50 dark:bg-red-900/20 dark:border-red-800/50">
            <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <p class="text-red-400 font-semibold">{{ count($overdueBorrows) }} Overdue Book(s)</p>
                <p class="text-sm mt-1 text-gray-600 dark:text-gray-400">You have overdue items. Please return them as soon as possible to avoid late fees and maintain your borrowing privileges.</p>
            </div>
        </div>
    @endif

    <!-- Stats & Subscription -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Subscription -->
        <div class="lg:col-span-2 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-8 border border-slate-700 shadow-xl">
            <h3 class="font-bold text-xl mb-6 text-white">Subscription Status</h3>
            @if($subscription)
                <div class="space-y-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-400 text-sm">Current Tier</p>
                            <p class="text-3xl font-bold text-white mt-1">{{ $subscription->membershipTier->name }}</p>
                        </div>
                        <span class="bg-emerald-500/20 text-emerald-400 px-4 py-1 rounded-full text-sm font-semibold border border-emerald-500/30">Active</span>
                    </div>
                    <div class="grid grid-cols-2 gap-8 pt-6 border-t border-slate-600">
                        <div>
                            <p class="text-slate-400 text-sm mb-2">Books/Week</p>
                            <p class="text-2xl font-bold text-white">{{ $subscription->membershipTier->borrow_limit_per_week }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm mb-2">Borrow Period</p>
                            <p class="text-2xl font-bold text-white">{{ $subscription->membershipTier->borrow_duration }} days</p>
                        </div>
                    </div>
                    <div class="pt-6 border-t border-slate-600">
                        <p class="text-slate-400 text-sm mb-2">Expires</p>
                        <p class="text-lg font-bold text-white">{{ $subscription->ends_at->format('M d, Y') }}</p>
                        <p class="text-sm text-slate-400">{{ $subscription->ends_at->diffForHumans() }}</p>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-20 h-20 mx-auto mb-6 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-lg font-semibold mb-4 text-slate-300">No Active Subscription</p>
                    <a href="{{ route('student.subscription.index') }}" class="inline-block px-8 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-lg">
                        Get Subscription
                    </a>
                </div>
            @endif
        </div>

        <!-- Stats -->
        <div class="space-y-4">
            <x-stat-card title="Active Borrows" value="{{ $activeBorrows->count() }}" subtitle="Currently out" color="blue" />
            <x-stat-card title="Reservations" value="{{ $reservations->count() }}" subtitle="In queue" color="purple" />
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Active Borrows -->
        <div class="lg:col-span-2">
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700/50 p-8 shadow-2xl">
                <h3 class="text-2xl font-bold mb-8" style="color: rgb(var(--text-primary));">Active Borrows</h3>
                @if($activeBorrows->count() > 0)
                    <div class="space-y-4">
                        @foreach($activeBorrows as $borrow)
                            <div class="group p-6 rounded-xl border border-slate-700 hover:border-blue-500/50 hover:bg-slate-800/70 transition-all shadow-md hover:shadow-2xl">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-bold text-lg mb-1 line-clamp-1" style="color: rgb(var(--text-primary));">{{ $borrow->book->title }}</h4>
                                        <p class="text-slate-400 mb-3">{{ $borrow->book->author }}</p>
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center gap-1 text-sm">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"></path>
                                                </svg>
                                                Due {{ $borrow->due_at->format('M d') }}
                                            </div>
                                            @php
                                                $daysLeft = $borrow->due_at->diffInDays(now(), false);
                                            @endphp
                                            @if($daysLeft < 0)
                                                <span class="px-3 py-1 bg-red-500/20 text-red-400 text-xs font-bold rounded-full border border-red-500/30">Overdue {{ abs($daysLeft) }}d</span>
                                            @elseif($daysLeft <= 3)
                                                <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 text-xs font-bold rounded-full border border-yellow-500/30">{{ $daysLeft }} days left</span>
                                            @endif
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('student.active-borrows.renew', $borrow) }}" class="flex-shrink-0" onsubmit="return confirm('Renew this borrow?')">
                                        @csrf
                                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold text-sm rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg">
                                            Renew
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 border-2 border-dashed border-slate-700 rounded-2xl">
                        <svg class="w-24 h-24 mx-auto mb-6 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 015.5 4c-1.255 0-2.443 .29-3.5 .804v10A7.969 7.969 0 015.5 14c1.669 0 3.218 .51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443 .29 3.5 .804v-10A7.968 7.968 0 0114.5 4c-1.669 0-3.218 .51-4.5 1.385A7.968 7.968 0 009 4.804z"></path>
                        </svg>
                        <h3 class="text-2xl font-bold mb-2" style="color: rgb(var(--text-primary));">No Active Borrows</h3>
                        <p class="text-slate-400 mb-8">Start borrowing from our collection</p>
                        <a href="{{ route('student.book-catalog.index') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Browse Books
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Reservations -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700/50 p-8 shadow-2xl">
                <h3 class="text-xl font-bold mb-6" style="color: rgb(var(--text-primary));">Reservations ({{ $reservations->count() }})</h3>
                @if($reservations->count() > 0)
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach($reservations as $reservation)
                            <div class="group p-5 rounded-xl border border-slate-700 hover:border-purple-500/50 hover:bg-slate-800/70 transition-all shadow-md hover:shadow-2xl">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-4 flex-1">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-xl flex items-center justify-center text-white font-bold text-lg flex-shrink-0 shadow-lg">
                                            {{ $loop->index + 1 }}
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="font-bold text-base line-clamp-1 mb-1" style="color: rgb(var(--text-primary));">{{ $reservation->book->title }}</h5>
                                            <p class="text-sm text-slate-400">{{ $reservation->book->author }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right flex flex-col items-end gap-2">
                                        <span class="px-3 py-1 bg-purple-500/20 text-purple-400 font-semibold text-xs rounded-full border border-purple-500/30">
                                            Waiting
                                        </span>
                                        <span class="text-xs text-slate-400">{{ $reservation->created_at->diffForHumans() }}</span>
                                        <form method="POST" action="{{ route('student.reservations.destroy', $reservation->id) }}" class="mt-1" onsubmit="return confirm('Cancel this reservation?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold text-sm rounded-lg shadow-lg hover:shadow-xl transition-all">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($reservations->count() > 5)
                        <div class="mt-4 pt-4 border-t border-slate-700 text-center">
                            <a href="#" class="text-purple-400 hover:text-purple-300 text-sm font-medium">View all reservations</a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-16 border-2 border-dashed border-slate-700 rounded-2xl">
                        <svg class="w-20 h-20 mx-auto mb-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <h4 class="text-xl font-bold mb-2" style="color: rgb(var(--text-primary));">No Reservations</h4>
                        <p class="text-slate-400 mb-8">Reserve books when copies are unavailable to be first in line</p>
                        <a href="{{ route('student.book-catalog.index') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-purple-500 to-indigo-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Find Books
                        </a>
                    </div>
                @endif
            </div>

            <!-- Account Stats -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700/50 p-6 shadow-xl">
                <h4 class="font-bold text-lg mb-6" style="color: rgb(var(--text-primary));">Quick Stats</h4>
                <div class="space-y-4 divide-y divide-slate-700 last:divide-y-0">
                    <div class="flex justify-between py-3">
                        <span class="text-slate-400">Member since</span>
                        <span class="font-bold" style="color: rgb(var(--text-primary));">{{ auth()->user()->created_at->format('M Y') }}</span>
                    </div>
                    <div class="flex justify-between py-3">
                        <span class="text-slate-400">Total borrowed</span>
                        <span class="font-bold text-green-400">{{ $totalBorrowed }}</span>
                    </div>
                    <div class="flex justify-between py-3">
                        <span class="text-slate-400">Late fees owed</span>
                        <span class="font-bold text-red-400">${{ number_format($lateFees, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-3">
                <a href="{{ route('student.book-catalog.index') }}" class="block w-full text-center py-4 px-6 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold text-lg rounded-2xl shadow-2xl hover:shadow-3xl hover:scale-[1.02] transition-all">
                    Browse Books →
                </a>
                <a href="{{ route('profile.edit') }}" class="block w-full text-center py-3 px-6 border-2 border-slate-600 text-slate-200 hover:border-slate-500 hover:bg-slate-800 font-semibold rounded-xl transition-all">
                    Profile Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Recommended Books -->
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700/50 p-8 shadow-2xl">
        <h3 class="text-2xl font-bold mb-8" style="color: rgb(var(--text-primary));">Recommended For You</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($recommendedBooks as $book)
                <div class="group cursor-pointer hover:shadow-2xl transition-all rounded-2xl overflow-hidden border border-slate-700 hover:border-emerald-500 bg-gradient-to-b from-slate-800/50 to-slate-900/30 hover:from-emerald-500/5">
                    <div class="h-48 p-8 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-20 h-20 opacity-20 group-hover:opacity-30 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 015.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0114.5 4c-1.669 0-3.218.51-4.5 1.385A7.968 7.968 0 009 4.804z"></path>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h5 class="font-bold line-clamp-2 mb-2 text-lg" style="color: rgb(var(--text-primary));">{{ $book->title }}</h5>
                        <p class="text-sm text-slate-400 mb-4">{{ $book->author }}</p>
                        <a href="{{ route('student.book-catalog.show', $book->id) }}" class="w-full block text-center py-3 px-6 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all">
                            Reserve Now
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20">
                    <svg class="w-24 h-24 mx-auto mb-6 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <h4 class="text-2xl font-bold mb-3" style="color: rgb(var(--text-primary));">No Recommendations</h4>
                    <p class="text-slate-400 text-lg">Come back later for personalized suggestions</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>

