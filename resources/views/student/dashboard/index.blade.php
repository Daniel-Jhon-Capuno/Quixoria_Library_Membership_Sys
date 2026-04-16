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
        <div class="mb-6 rounded-xl p-6 flex items-start gap-4 bg-danger/20 border border-danger/50">
            <svg class="w-6 h-6 text-danger flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <p class="text-red-300 font-semibold">{{ count($overdueBorrows) }} Overdue Book(s)</p>
                <p class="text-sm mt-1" style="color: rgb(var(--text-secondary));">You have overdue items. Please return them as soon as possible to avoid late fees and maintain your borrowing privileges.</p>
            </div>
        </div>
    @endif

    <!-- Subscription Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Subscription Card -->
        <div class="lg:col-span-2 rounded-xl p-6 shadow-card"
             style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
            <h3 class="font-semibold mb-4" style="color: rgb(var(--text-primary));">Subscription Status</h3>
            @if($subscription ?? null)
                <div class="space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm" style="color: rgb(var(--text-secondary));">Current Tier</p>
                            <p class="text-2xl font-bold text-primary mt-1">{{ $subscription->membershipTier->name ?? 'N/A' }}</p>
                        </div>
                        <span class="bg-primary/20 text-primary px-3 py-1 rounded text-sm font-medium">Active</span>
                    </div>
                    <div style="border-top-color: rgb(var(--border-primary));" class="border-t pt-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs" style="color: rgb(var(--text-secondary));">Books per Month</p>
                                <p class="text-lg font-bold mt-1" style="color: rgb(var(--text-primary));">{{ $subscription->membershipTier->books_per_month ?? 0 }}</p>
                            </div>
                            <div>
                                <p class="text-xs" style="color: rgb(var(--text-secondary));">Borrow Period</p>
                                <p class="text-lg font-bold mt-1" style="color: rgb(var(--text-primary));">{{ $subscription->membershipTier->borrow_duration ?? 14 }} days</p>
                            </div>
                        </div>
                    </div>
                    <div style="border-top-color: rgb(var(--border-primary));" class="border-t pt-4">
                        <p class="text-xs" style="color: rgb(var(--text-secondary));">Expires</p>
                        <p class="font-semibold mt-1" style="color: rgb(var(--text-primary));">{{ $subscription->ends_at->format('M d, Y') ?? 'N/A' }}</p>
                        <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));">{{ $subscription->ends_at->diffForHumans() ?? 'N/A' }}</p>
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <p class="mb-4" style="color: rgb(var(--text-secondary));">You don't have an active subscription</p>
                    <a href="{{ route('student.subscription.index') }}" class="inline-block px-4 py-2 bg-primary hover:bg-primary-dark rounded-lg text-white font-medium transition">
                        Get Subscription
                    </a>
                </div>
            @endif
        </div>

        <!-- Quick Stats -->
        <div class="space-y-4">
            <x-stat-card 
                title="My Borrows" 
                value="{{ $activeBorrows ?? 0 }}"
                subtitle="Currently borrowed" 
                color="secondary" />
            <x-stat-card 
                title="Reservations" 
                value="{{ $reservations ?? 0 }}"
                subtitle="Pending books" 
                color="accent" />
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Active Borrowings -->
        <div class="lg:col-span-2">
            <div class="rounded-xl p-6 shadow-card"
                 style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
                <h3 class="font-semibold mb-6" style="color: rgb(var(--text-primary));">My Active Borrows</h3>
                @if(($activeBorrows ?? collect())->count() > 0)
                    <div class="space-y-3">
                        @foreach($activeBorrows as $borrow)
                            <div class="rounded-lg p-4 flex items-start justify-between transition"
                                 style="background-color: rgb(var(--bg-secondary)); border: 1px solid rgb(var(--border-primary) / 0.5); border-color: rgb(var(--border-primary) / 0.5);"
                                 class="hover:border-primary/50">
                                <div class="flex-1">
                                    <p class="font-semibold" style="color: rgb(var(--text-primary));">{{ $borrow->book->title ?? 'N/A' }}</p>
                                    <p class="text-sm" style="color: rgb(var(--text-secondary));">by {{ $borrow->book->author ?? 'Unknown' }}</p>
                                    <div class="mt-3 flex items-center gap-2">
                                        @php
                                            $daysUntilDue = now()->diffInDays($borrow->due_at ?? now());
                                            $isNearOverdue = $daysUntilDue <= 3;
                                        @endphp
                                        <span class="text-xs" style="color: @if($isNearOverdue) #f59e0b @else rgb(var(--text-secondary)) @endif;">
                                            Due: {{ ($borrow->due_at ?? now())->format('M d') }}
                                        </span>
                                        @if($isNearOverdue && $daysUntilDue > 0)
                                            <span class="bg-warning/20 text-warning px-2 py-0.5 rounded text-xs font-medium">{{ $daysUntilDue }} days left</span>
                                        @elseif($daysUntilDue <= 0)
                                            <span class="bg-danger/20 text-red-400 px-2 py-0.5 rounded text-xs font-medium">OVERDUE</span>
                                        @endif
                                    </div>
                                </div>
                                <button class="px-3 py-2 rounded transition text-sm font-medium bg-primary/20 hover:bg-primary/30 text-primary">
                                    Renew
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8" style="color: rgb(var(--text-secondary));">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 015.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0114.5 4c-1.669 0-3.218.51-4.5 1.385A7.968 7.968 0 009 4.804z"></path>
                        </svg>
                        <p>You haven't borrowed any books yet</p>
                        <a href="{{ route('student.book-catalog.index') }}" class="mt-3 text-primary hover:text-primary-dark transition font-medium">Browse our collection →</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <!-- Reservations -->
            <div class="rounded-xl p-6 shadow-card"
                 style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
                <h4 class="font-semibold mb-4" style="color: rgb(var(--text-primary));">Reservations</h4>
                @if(($reservations ?? collect())->count() > 0)
                    <div class="space-y-3">
                        @foreach($reservations as $reservation)
                            <div class="flex items-start gap-3">
                                <span class="text-primary font-bold">{{ $loop->index + 1 }}.</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate" style="color: rgb(var(--text-primary));">{{ $reservation->book->title ?? 'N/A' }}</p>
                                    <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));">{{ $reservation->created_at->diffForHumans() ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-sm py-4" style="color: rgb(var(--text-secondary));">No active reservations</p>
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="rounded-xl p-6 shadow-card"
                 style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
                <h4 class="font-semibold mb-4" style="color: rgb(var(--text-primary));">Account</h4>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span style="color: rgb(var(--text-secondary));">Member Since</span>
                        <span style="color: rgb(var(--text-primary));">{{ auth()->user()->created_at->format('M Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm" style="border-top-color: rgb(var(--border-primary)));" class="border-t pt-3">
                        <span style="color: rgb(var(--text-secondary));">Total Borrowed</span>
                        <span class="font-semibold" style="color: rgb(var(--text-primary));">{{ $totalBorrowed ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between text-sm" style="border-top-color: rgb(var(--border-primary));" class="border-t pt-3">
                        <span style="color: rgb(var(--text-secondary));">Late Fees</span>
                        <span style="color: rgb(var(--text-primary));">${{ number_format($lateFees ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-2">
                <a href="{{ route('student.book-catalog.index') }}" class="block w-full px-4 py-3 rounded-lg text-white font-medium transition text-center bg-primary hover:bg-primary-dark">
                    Browse Books
                </a>
                <a href="{{ route('profile.edit') }}" class="block w-full px-4 py-3 rounded-lg font-medium transition text-center border border-accent text-accent bg-accent/20 hover:bg-accent/30">
                    View Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Recommended Books -->
    <div class="rounded-xl p-6 shadow-card"
         style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
        <h3 class="font-semibold mb-6" style="color: rgb(var(--text-primary));">Recommended For You</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($recommendedBooks ?? [] as $book)
                <div class="rounded-lg p-4 cursor-pointer group transition"
                     style="background-color: rgb(var(--bg-secondary)); border: 1px solid rgb(var(--border-primary) / 0.5);"
                     class="hover:border-primary/50">
                    <div class="mb-4 rounded-lg p-8 flex items-center justify-center h-32 transition"
                         style="background: linear-gradient(to bottom right, rgba(236, 72, 153, 0.2), rgba(6, 182, 212, 0.2));"
                         class="group-hover:from-primary/30 group-hover:to-accent/30">
                        <svg class="w-12 h-12 opacity-40" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 015.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0114.5 4c-1.669 0-3.218.51-4.5 1.385A7.968 7.968 0 009 4.804z"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold line-clamp-2" style="color: rgb(var(--text-primary));">{{ $book->title ?? 'N/A' }}</p>
                    <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));">{{ $book->author ?? 'Unknown' }}</p>
                    <a href="{{ route('student.book-catalog.show', $book->id) }}" class="w-full mt-3 px-3 py-2 rounded text-xs font-medium transition bg-primary/20 hover:bg-primary/30 text-primary inline-block text-center">
                        + Borrow
                    </a>
                </div>
            @empty
                <div class="col-span-4 text-center py-8" style="color: rgb(var(--text-secondary));">
                    <p>No recommendations at this time</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>