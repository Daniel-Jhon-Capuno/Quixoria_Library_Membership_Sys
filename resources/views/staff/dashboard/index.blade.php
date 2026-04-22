<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-100">Staff Dashboard</h1>
                <p class="text-sm mt-1 text-gray-400">Manage member requests and library operations</p>
            </div>
        </div>
    </x-slot>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="rounded-xl p-4 mb-6 bg-slate-800 border border-slate-700 text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl p-4 mb-6 bg-slate-800 border border-slate-700 text-red-200">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl p-4 mb-6 bg-slate-800 border border-slate-700 text-yellow-200">
            <ul class="list-disc pl-5"> 
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
        <div class="card mb-8" style="border: 1px solid rgba(220,40,40,0.08);">
            <div class="flex items-center gap-2 mb-6">
                <svg class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="font-semibold text-lg text-gray-100">Overdue Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="border-bottom-color: rgb(var(--border-primary));" class="border-b">
                            <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Member</th>
                            <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Book</th>
                            <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Due Date</th>
                            <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Days Overdue</th>
                            <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdueBorrows as $borrow)
                            <tr class="border-b transition border-slate-700 bg-slate-800 hover:bg-slate-700/50 text-gray-100">
                                <td class="py-3 px-4">{{ $borrow->user->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4 text-gray-400">{{ $borrow->book->title ?? 'N/A' }}</td>
                                <td class="py-3 px-4 text-gray-400">{{ $borrow->due_at->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="py-3 px-4"><span class="bg-red-900/30 text-red-300 px-2 py-1 rounded text-xs font-medium">{{ now()->diffInDays($borrow->due_at) }} days</span></td>
                                <td class="py-3 px-4">
                                    <form method="POST" action="{{ route('staff.deadline-dashboard.ping', $borrow->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" data-confirm="Send reminder to this student?" class="text-gray-100 hover:text-gray-300 transition text-sm font-medium">Send Reminder</button>
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
            <div class="card">
            <div class="card-body">
            <h3 class="font-semibold mb-6 text-gray-100">Pending Borrow Requests</h3>
            <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="border-bottom-color: rgb(var(--border-primary));" class="border-b">
                                <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Member</th>
                                <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Tier</th>
                                <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Book</th>
                                <th class="text-left font-medium text-sm py-3 px-4 text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingRequests ?? [] as $request)
                                <tr class="border-b transition border-slate-700 bg-slate-800 hover:bg-slate-700/50 text-gray-100">
                                    <td class="py-3 px-4">{{ $request->user->name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4"><span class="bg-slate-700 text-gray-100 px-2 py-1 rounded text-xs font-medium">{{ $request->user->subscription->membershipTier->name ?? 'None' }}</span></td>
                                    <td class="py-3 px-4 text-gray-400">{{ $request->book->title ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 flex gap-2">
                                        <a href="{{ route('staff.borrow-requests.confirm.form', $request->id) }}" class="text-gray-100 hover:text-gray-300 transition text-sm font-medium">Approve</a>
                                        <span class="text-gray-400">|</span>
                                        <a href="{{ route('staff.borrow-requests.reject.form', $request->id) }}" class="text-red-400 hover:text-red-500 transition text-sm font-medium">Reject</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-gray-400">No pending requests</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
                </div>
        </div>

        <!-- Quick Stats -->
            <div class="space-y-4">
            <div class="card">
                <div class="card-body">
                <h4 class="font-semibold mb-4 text-gray-100">Quick Actions</h4>
                <div class="space-y-3">
                    <a href="{{ route('staff.borrow-requests.index') }}" class="block w-full px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-gray-100 font-medium transition text-center text-sm">View All Requests</a>
                    <button class="block w-full px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-gray-100 font-medium transition text-center text-sm" onclick="document.querySelector('[data-overdue-list]').scrollIntoView({behavior: 'smooth'})">Overdue List</button>
                    <a href="{{ route('admin.users.index') }}" class="block w-full px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-gray-100 font-medium transition text-center text-sm">Member Search</a>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Due Today Section -->
    @if(($dueTodayBorrows ?? collect())->count() > 0)
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 shadow-card">
            <h3 class="text-white font-semibold mb-6">Returns Due Today</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($dueTodayBorrows as $borrow)
                    <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                        <p class="text-gray-400 text-xs mb-2">{{ $borrow->user->name }}</p>
                        <p class="text-gray-100 font-semibold mb-2">{{ $borrow->book->title }}</p>
                        <p class="text-gray-400 text-sm mb-3">Due: Today</p>
                        <button class="w-full px-3 py-2 bg-red-900/20 hover:bg-red-900/30 text-red-300 rounded text-sm font-medium transition">
                            Send Reminder
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>