<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold" style="color: rgb(var(--text-primary));">Staff Dashboard</h1>
                <p class="text-sm mt-1" style="color: rgb(var(--text-secondary));">Manage member requests and library operations</p>
            </div>
        </div>
    </x-slot>

    <!-- Priority Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stat-card 
            title="Pending Requests" 
            value="{{ $pendingCount ?? 0 }}"
            subtitle="Awaiting approval"
            color="warning" />

        <x-stat-card 
            title="Active Borrows" 
            value="{{ $activeCount ?? 0 }}"
            subtitle="Currently borrowed"
            color="secondary" />

        <x-stat-card 
            title="Overdue Items" 
            value="{{ $overdueCount ?? 0 }}"
            subtitle="URGENT attention needed"
            color="danger" />

        <x-stat-card 
            title="Due Today" 
            value="{{ $dueTodayCount ?? 0 }}"
            subtitle="Return deadline today"
            color="accent" />
    </div>

    <!-- Overdue Borrows (High Priority) -->
    @if(($overdueBorrows ?? collect())->count() > 0)
        <div class="rounded-xl p-6 shadow-card mb-8"
             style="background-color: rgb(var(--surface-primary)); border: 1px solid rgba(239, 68, 68, 0.5);">
            <div class="flex items-center gap-2 mb-6">
                <svg class="w-6 h-6 text-danger" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="font-semibold text-lg" style="color: rgb(var(--text-primary));">Overdue Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="border-bottom-color: rgb(var(--border-primary));" class="border-b">
                            <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Member</th>
                            <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Book</th>
                            <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Due Date</th>
                            <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Days Overdue</th>
                            <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdueBorrows as $borrow)
                            <tr class="border-b transition" style="border-color: rgb(var(--border-primary) / 0.6); color: rgb(var(--text-primary));" class="hover:bg-dark-bg/50">
                                <td class="py-3 px-4">{{ $borrow->user->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4" style="color: rgb(var(--text-secondary));">{{ $borrow->book->title ?? 'N/A' }}</td>
                                <td class="py-3 px-4" style="color: rgb(var(--text-secondary));">{{ $borrow->due_at->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="py-3 px-4"><span class="bg-danger/20 text-red-400 px-2 py-1 rounded text-xs font-medium">{{ now()->diffInDays($borrow->due_at) }} days</span></td>
                                <td class="py-3 px-4">
                                    <form method="POST" action="#" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="text-primary hover:text-primary-dark transition text-sm font-medium">Send Reminder</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Pending Requests Table -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Requests Table -->
        <div class="lg:col-span-2">
            <div class="rounded-xl p-6 shadow-card"
                 style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
                <h3 class="font-semibold mb-6" style="color: rgb(var(--text-primary));">Pending Borrow Requests</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="border-bottom-color: rgb(var(--border-primary));" class="border-b">
                                <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Member</th>
                                <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Tier</th>
                                <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Book</th>
                                <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingRequests ?? [] as $request)
                                <tr class="border-b transition" style="border-color: rgb(var(--border-primary) / 0.6); color: rgb(var(--text-primary));" class="hover:bg-dark-bg/50">
                                    <td class="py-3 px-4">{{ $request->user->name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4"><span class="bg-accent/20 text-accent px-2 py-1 rounded text-xs font-medium">{{ $request->user->subscription->membershipTier->name ?? 'None' }}</span></td>
                                    <td class="py-3 px-4" style="color: rgb(var(--text-secondary));">{{ $request->book->title ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 flex gap-2">
                                        <button class="text-secondary hover:text-secondary/80 transition text-sm font-medium">Approve</button>
                                        <span style="color: rgb(var(--text-secondary));">/</span>
                                        <button class="text-danger hover:text-red-500 transition text-sm font-medium">Reject</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6" style="color: rgb(var(--text-secondary));">No pending requests</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="space-y-4">
            <div class="rounded-xl p-6 shadow-card"
                 style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
                <h4 class="font-semibold mb-4" style="color: rgb(var(--text-primary));">Quick Actions</h4>
                <div class="space-y-3">
                    <a href="#" class="block w-full px-4 py-2 bg-primary hover:bg-primary-dark rounded-lg text-white font-medium transition text-center text-sm">View All Requests</a>
                    <a href="#" class="block w-full px-4 py-2 bg-accent hover:bg-blue-600 rounded-lg text-white font-medium transition text-center text-sm">Overdue List</a>
                    <a href="#" class="block w-full px-4 py-2 bg-secondary hover:bg-cyan-500 rounded-lg text-white font-medium transition text-center text-sm">Member Search</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Due Today Section -->
    @if(($dueTodayBorrows ?? collect())->count() > 0)
        <div class="bg-dark-surface border border-dark-border rounded-xl p-6 shadow-card">
            <h3 class="text-white font-semibold mb-6">Returns Due Today</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($dueTodayBorrows as $borrow)
                    <div class="bg-dark-bg border border-dark-border rounded-lg p-4">
                        <p class="text-slate-400 text-xs mb-2">{{ $borrow->user->name }}</p>
                        <p class="text-white font-semibold mb-2">{{ $borrow->book->title }}</p>
                        <p class="text-slate-400 text-sm mb-3">Due: Today</p>
                        <button class="w-full px-3 py-2 bg-warning/20 hover:bg-warning/30 text-warning rounded text-sm font-medium transition">
                            Send Reminder
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>
