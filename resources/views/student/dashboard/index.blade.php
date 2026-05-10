<x-app-layout>
    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInFromTop {
            from {
                opacity: 0;
                transform: translateY(-30px) scaleY(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scaleY(1);
            }
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.6s ease-out;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.7s ease-out 0.1s both;
        }

        .animate-slide-in-from-top {
            animation: slideInFromTop 0.5s ease-out;
        }

        .animate-pop-in {
            animation: popIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .dashboard-card {
            animation: popIn 0.6s ease-out backwards;
        }

        .dashboard-card:nth-child(1) { animation-delay: 0.1s; }
        .dashboard-card:nth-child(2) { animation-delay: 0.2s; }
        .dashboard-card:nth-child(3) { animation-delay: 0.3s; }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold animate-fade-in-down" style="color: rgb(var(--text-primary));">My Library Dashboard</h1>
                <p class="text-sm mt-1 animate-fade-in-up" style="color: rgb(var(--text-secondary));">Manage your books, subscriptions, and reservations</p>
            </div>
        </div>
    </x-slot>

    <div class="student-dashboard">
    @php
        $activeBorrowItems = collect($activeBorrows ?? []);
        $reservationItems = collect($reservations ?? []);
        $reservationCount = $reservationItems->count();
        $atRiskBorrows = $activeBorrowItems->filter(function ($borrow) {
            return optional($borrow->due_at)->isPast() || now()->startOfDay()->diffInDays($borrow->due_at->copy()->startOfDay(), false) <= 3;
        })->count();
    @endphp

    <!-- Alert Banners -->
    @if(($overdueBorrows ?? collect())->count() > 0)
        <div class="mb-6 rounded-xl p-6 flex items-start gap-4 bg-red-50/20 border border-red-200/50 animate-slide-in-from-top">
            <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <p class="text-red-400 font-semibold">{{ count($overdueBorrows) }} Overdue Book(s)</p>
                <p class="text-sm mt-1 text-red-100/90">You have overdue items. Please return them as soon as possible to avoid late fees and keep your borrowing privileges active.</p>
            </div>
        </div>
    @endif

    <div class="mb-8 rounded-2xl border border-cyan-400/20 bg-gradient-to-r from-cyan-500/10 via-slate-900/40 to-blue-500/10 p-4 md:p-5 animate-fade-in-up">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-cyan-300/80">Student Hub</p>
                <p class="text-sm text-slate-300 mt-1">Everything you need for borrowing, renewals, and reservations in one place.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('student.book-catalog.index') }}" class="px-4 py-2 rounded-xl bg-cyan-500/20 border border-cyan-400/30 text-cyan-100 text-sm font-semibold hover:bg-cyan-500/30 transition">Browse Books</a>
                <a href="{{ route('student.active-borrows.index') }}" class="px-4 py-2 rounded-xl bg-amber-500/20 border border-amber-400/30 text-amber-100 text-sm font-semibold hover:bg-amber-500/30 transition">My Borrows</a>
                <a href="{{ route('student.subscription.index') }}" class="px-4 py-2 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-100 text-sm font-semibold hover:bg-emerald-500/30 transition">Subscription</a>
            </div>
        </div>
    </div>

    <!-- Stats & Subscription -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 animate-fade-in-up">
        <!-- Subscription -->
        <div class="dashboard-card lg:col-span-2 bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-8 border border-slate-700 shadow-xl hover:shadow-2xl transition-all duration-300">
            <h3 class="font-bold text-xl mb-6 text-white">Subscription Status</h3>
            @if($subscription)
                <div class="space-y-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-400 text-sm">Current Tier</p>
                            <p class="text-2xl md:text-3xl font-bold text-white mt-1">{{ $subscription->membershipTier->name }}</p>
                        </div>
                        <span class="bg-emerald-500/20 text-emerald-400 px-4 py-1 rounded-full text-sm font-semibold border border-emerald-500/30">Active</span>
                    </div>
                    <div class="grid grid-cols-2 gap-8 pt-6 border-t border-slate-600">
                        <div>
                            <p class="text-slate-400 text-sm mb-2">Books/Week</p>
                            <p class="text-xl md:text-2xl font-bold text-white">{{ $subscription->membershipTier->borrow_limit_per_week }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-sm mb-2">Borrow Period</p>
                            <p class="text-xl md:text-2xl font-bold text-white">{{ $subscription->membershipTier->borrow_duration }} days</p>
                        </div>
                    </div>
                    <div class="pt-6 border-t border-slate-600">
                        <p class="text-slate-400 text-sm mb-2">Expires</p>
                        @if($subscription->ends_at)
                            <p class="text-base md:text-lg font-bold text-white">{{ $subscription->ends_at->format('M d, Y') }}</p>
                            <p class="text-sm text-slate-400">{{ $subscription->ends_at->diffForHumans() }}</p>
                        @else
                            <p class="text-base md:text-lg font-bold text-white">Unlimited</p>
                            <p class="text-sm text-slate-400">Basic (free) plan — no expiration</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-20 h-20 mx-auto mb-6 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-lg font-semibold mb-4 text-slate-300">No Active Subscription</p>
                    <a href="{{ route('student.subscription.index') }}" class="w-full md:w-auto min-h-[44px] inline-block px-8 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-lg">
                        Get Subscription
                    </a>
                </div>
            @endif
        </div>

        <!-- Stats -->
        <div class="space-y-4">
            <div class="dashboard-card">
                <x-stat-card title="Active Borrows" value="{{ $activeBorrowItems->count() }}" subtitle="Currently out" color="blue" />
            </div>
            <div class="dashboard-card">
                <x-stat-card title="Reservations" value="{{ $reservationCount }}" subtitle="In queue" color="purple" />
            </div>
            <div class="dashboard-card bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700/50 p-5 shadow-xl">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">At-Risk Borrows</p>
                <p class="text-3xl font-bold mt-2 {{ $atRiskBorrows > 0 ? 'text-amber-300' : 'text-emerald-300' }}">{{ $atRiskBorrows }}</p>
                <p class="text-xs mt-1 text-slate-400">Overdue or due within 3 days</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in-up" style="animation-delay: 0.3s;">
        <!-- Active Borrows -->
        <div class="lg:col-span-2 dashboard-card">
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700/50 p-8 shadow-2xl">
                <div class="mb-8 flex items-end justify-between gap-4">
                    <div>
                        <h3 class="text-xl md:text-2xl font-bold" style="color: rgb(var(--text-primary));">Active Borrows</h3>
                        <p class="text-sm mt-1 text-slate-400">Renew items early to avoid penalties.</p>
                    </div>
                </div>
                @if($activeBorrowItems->count() > 0)
                    <div class="space-y-4">
                        @foreach($activeBorrowItems as $borrow)
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
                                                $daysDelta = (int) now()->startOfDay()->diffInDays($borrow->due_at->copy()->startOfDay(), false);
                                            @endphp
                                            @if($daysDelta < 0)
                                                <span class="px-3 py-1 bg-red-500/20 text-red-400 text-xs font-bold rounded-full border border-red-500/30">Overdue {{ abs($daysDelta) }}d</span>
                                            @elseif($daysDelta === 0)
                                                <span class="px-3 py-1 bg-amber-500/20 text-amber-300 text-xs font-bold rounded-full border border-amber-500/30">Due Today</span>
                                            @elseif($daysDelta <= 3)
                                                <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 text-xs font-bold rounded-full border border-yellow-500/30">{{ $daysDelta }} days left</span>
                                            @endif
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('student.active-borrows.renew', $borrow) }}" class="flex-shrink-0" data-confirm="Renew this borrow?">
                                        @csrf
                                        <button type="submit" class="w-full md:w-auto min-h-[44px] px-6 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold text-sm rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg">
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
                        <a href="{{ route('student.book-catalog.index') }}" class="w-full md:w-auto min-h-[44px] inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
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
        <div class="space-y-6 animate-fade-in-up" style="animation-delay: 0.4s;">
            <!-- Account Stats -->
            <div class="dashboard-card bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700/50 p-6 shadow-xl">
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
                        <span class="font-bold text-red-400">₱{{ number_format($lateFees, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-3">
            </div>
        </div>
    </div>

    <!-- Recommended Books -->
    <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl border border-slate-700/50 p-8 shadow-2xl">
        <h3 class="text-xl md:text-2xl font-bold mb-8" style="color: rgb(var(--text-primary));">Recommended For You</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($recommendedBooks as $book)
                <div class="group cursor-pointer hover:shadow-2xl transition-all rounded-2xl overflow-hidden border border-slate-700 hover:border-gray-500 bg-gradient-to-b from-slate-800/50 to-slate-900/30 hover:from-gray-500/5">
                    <div class="h-48 p-8 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <svg class="w-20 h-20 opacity-20 group-hover:opacity-30 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 015.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0114.5 4c-1.669 0-3.218.51-4.5 1.385A7.968 7.968 0 009 4.804z"></path>
                        </svg>
                    </div>
                    <div class="p-8">
                        <h5 class="font-bold line-clamp-2 mb-2 text-base md:text-lg" style="color: rgb(var(--text-primary));">{{ $book->title }}</h5>
                        <p class="text-sm text-slate-400 mb-4">{{ $book->author }}</p>
                        <a href="{{ route('student.book-catalog.show', $book->id) }}" class="w-full block text-center py-3 px-6 bg-black hover:bg-gray-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all min-h-[44px]">
                            Reserve Now
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20">
                    <svg class="w-24 h-24 mx-auto mb-6 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <h4 class="text-xl md:text-2xl font-bold mb-3" style="color: rgb(var(--text-primary));">No Recommendations</h4>
                    <p class="text-slate-400 text-base md:text-lg">Come back later for personalized suggestions</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8 pt-5 border-t border-slate-700/70 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold" style="color: rgb(var(--text-primary));">Want more options?</p>
                <p class="text-sm text-slate-400 mt-1">Browse the full catalog to find books you can borrow or reserve next.</p>
            </div>
            <a href="{{ route('student.book-catalog.index') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold shadow-lg hover:shadow-xl transition-all min-h-[44px]">
                Browse Full Catalog
            </a>
        </div>
    </div>
    </div>
</x-app-layout>

